<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\ProjectStatus;
use App\Enums\ResearchCategory;
use App\Enums\UserRole;
use App\Livewire\PublicRepository;
use App\Livewire\ResearchFolder;
use App\Livewire\ResearchGapExplorer;
use App\Livewire\ResearchRecord;
use App\Models\Project;
use App\Models\ResearchDomain;
use App\Models\ResearchGap;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ArchiveDiscoveryTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;

    protected User $supervisor;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (UserRole::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
        }

        $this->student = User::factory()->create(['name' => 'Kelvin Mwape', 'role' => UserRole::STUDENT]);
        $this->student->assignRole(UserRole::STUDENT->value);

        $this->supervisor = User::factory()->create(['name' => 'Dr. C. Mulenga', 'role' => UserRole::SUPERVISOR]);
        $this->supervisor->assignRole(UserRole::SUPERVISOR->value);
    }

    protected function makeProject(string $title, ProjectStatus $status = ProjectStatus::COMPLETED): Project
    {
        return Project::create([
            'title' => $title,
            'abstract' => 'An abstract describing the study in enough detail to be catalogued.',
            'category' => ResearchCategory::CROP_SCIENCE,
            'status' => $status,
            'student_id' => $this->student->id,
            'supervisor_id' => $this->supervisor->id,
            'completion_year' => 2025,
            'full_document_path' => 'manuscripts/secret.pdf',
        ]);
    }

    public function test_completed_record_detail_page_shows_metadata_but_not_storage_paths(): void
    {
        $project = $this->makeProject('Cassava Mosaic Resistance Screening');

        $this->get(route('repository.show', $project))
            ->assertOk()
            ->assertSee('Cassava Mosaic Resistance Screening')
            ->assertSee('Dr. C. Mulenga')
            ->assertSee('Abstract and research gaps only')
            ->assertDontSee('manuscripts/secret.pdf')
            ->assertDontSee('Download manuscript');
    }

    public function test_in_progress_records_are_hidden_from_the_public_but_visible_to_their_author(): void
    {
        $project = $this->makeProject('Unfinished Irrigation Study', ProjectStatus::UNDER_SUPERVISION);

        $this->get(route('repository.show', $project))->assertNotFound();

        $peer = User::factory()->create(['role' => UserRole::STUDENT]);
        $peer->assignRole(UserRole::STUDENT->value);
        $this->actingAs($peer)->get(route('repository.show', $project))->assertNotFound();

        $this->actingAs($this->student)->get(route('repository.show', $project))
            ->assertOk()
            ->assertSee('Download manuscript');
    }

    public function test_landing_page_never_lists_in_progress_projects(): void
    {
        $this->makeProject('Catalogued Soil Study');
        $this->makeProject('Private Proposal Draft', ProjectStatus::PROPOSAL_SUBMITTED);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Catalogued Soil Study')
            ->assertDontSee('Private Proposal Draft');
    }

    public function test_search_can_target_author_and_supervisor_fields(): void
    {
        $this->makeProject('Maize Stover Biochar Trials');

        Livewire::test(PublicRepository::class)
            ->set('field', 'author')
            ->set('search', 'Kelvin')
            ->assertSee('Maize Stover Biochar Trials')
            ->set('search', 'Mulenga')
            ->assertDontSee('Maize Stover Biochar Trials')
            ->set('field', 'supervisor')
            ->assertSee('Maize Stover Biochar Trials');
    }

    public function test_gap_explorer_only_lists_gaps_from_completed_projects(): void
    {
        $domain = ResearchDomain::create(['name' => 'Soil Health', 'slug' => 'soil-health']);

        ResearchGap::create([
            'project_id' => $this->makeProject('Public Study')->id,
            'domain_id' => $domain->id,
            'extracted_gap_text' => 'Long-term biochar ageing effects remain unmeasured.',
            'is_emerging' => true,
        ]);

        ResearchGap::create([
            'project_id' => $this->makeProject('Hidden Study', ProjectStatus::UNDER_REVIEW)->id,
            'domain_id' => $domain->id,
            'extracted_gap_text' => 'Confidential gap from an unfinished study.',
            'is_emerging' => true,
        ]);

        $this->get(route('gaps'))->assertOk();

        Livewire::test(ResearchGapExplorer::class)
            ->assertSee('Long-term biochar ageing effects remain unmeasured.')
            ->assertDontSee('Confidential gap from an unfinished study.')
            ->set('signal', 'saturated')
            ->assertDontSee('Long-term biochar ageing effects remain unmeasured.');
    }

    public function test_signed_in_users_can_save_records_to_their_folder(): void
    {
        $project = $this->makeProject('Fall Armyworm Biocontrol');

        $this->get(route('folder'))->assertRedirect(route('login'));

        $this->actingAs($this->student);

        Livewire::test(ResearchRecord::class, ['project' => $project])
            ->call('toggleSave', $project->id)
            ->assertSee('Saved to folder');

        $this->assertTrue($this->student->savedProjects()->whereKey($project->id)->exists());

        Livewire::test(ResearchFolder::class)
            ->assertSee('Fall Armyworm Biocontrol')
            ->call('toggleSave', $project->id)
            ->assertSee('Your folder is empty');
    }

    public function test_unfinished_projects_cannot_be_saved_to_a_folder(): void
    {
        $project = $this->makeProject('Draft Topic', ProjectStatus::DRAFT);

        $this->actingAs($this->student);

        $this->expectException(ModelNotFoundException::class);

        try {
            Livewire::test(PublicRepository::class)->call('toggleSave', $project->id);
        } finally {
            $this->assertFalse($this->student->savedProjects()->exists());
        }
    }
}
