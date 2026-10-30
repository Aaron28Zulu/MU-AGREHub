<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\ProjectStatus;
use App\Enums\ResearchCategory;
use App\Enums\UserRole;
use App\Livewire\PublicRepository;
use App\Models\Project;
use App\Models\ResearchDomain;
use App\Models\ResearchGap;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PublicRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (UserRole::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
        }
    }

    public function test_it_renders_public_repository_page_successfully(): void
    {
        $student = User::factory()->create(['name' => 'Student A', 'role' => UserRole::STUDENT]);
        Project::create([
            'title' => 'Soil Microbiome Dynamics in Biochar Amended Plots',
            'abstract' => 'Comprehensive soil microbiome evaluation.',
            'category' => ResearchCategory::AGRICULTURE,
            'status' => ProjectStatus::COMPLETED,
            'student_id' => $student->id,
            'completion_year' => 2025,
        ]);

        $response = $this->get(route('repository'));

        $response->assertOk();
        $response->assertSee('Institutional Research Archive');
        $response->assertSee('Soil Microbiome Dynamics');
        $response->assertSee('Faceted Filter Index');
    }

    public function test_it_filters_projects_reactively_by_search(): void
    {
        $student = User::factory()->create(['name' => 'Student B', 'role' => UserRole::STUDENT]);

        Project::create([
            'title' => 'Entomopathogenic Fungi for Fall Armyworm',
            'abstract' => 'Evaluating bioassays in maize trials.',
            'category' => ResearchCategory::ENVIRONMENTAL_SCIENCE,
            'status' => ProjectStatus::COMPLETED,
            'student_id' => $student->id,
            'completion_year' => 2025,
        ]);

        Project::create([
            'title' => 'Poultry Nutrition Formulation',
            'abstract' => 'Insect meal trial for broilers.',
            'category' => ResearchCategory::NATURAL_SCIENCES,
            'status' => ProjectStatus::COMPLETED,
            'student_id' => $student->id,
            'completion_year' => 2024,
        ]);

        Livewire::test(PublicRepository::class)
            ->set('search', 'Fall Armyworm')
            ->assertSee('Entomopathogenic Fungi for Fall Armyworm')
            ->assertDontSee('Poultry Nutrition Formulation');
    }

    public function test_it_filters_projects_by_category_and_gaps_only(): void
    {
        $student = User::factory()->create(['name' => 'Student C', 'role' => UserRole::STUDENT]);

        $projectWithGap = Project::create([
            'title' => 'Broiler Performance Under Black Soldier Fly Meal',
            'abstract' => 'Feeding trials assessing daily weight gain.',
            'category' => ResearchCategory::NATURAL_SCIENCES,
            'status' => ProjectStatus::COMPLETED,
            'student_id' => $student->id,
            'completion_year' => 2024,
        ]);

        $domain = ResearchDomain::create([
            'name' => 'Livestock Nutrition',
            'slug' => 'livestock-nutrition',
        ]);

        ResearchGap::create([
            'project_id' => $projectWithGap->id,
            'domain_id' => $domain->id,
            'extracted_gap_text' => 'Omega-3 fatty acid profile enhancement through substrate manipulation.',
            'is_emerging' => true,
        ]);

        $projectWithoutGap = Project::create([
            'title' => 'Tomato Supply Chain Efficiency',
            'abstract' => 'Evaluating digital cooperative platforms.',
            'category' => ResearchCategory::ECONOMICS,
            'status' => ProjectStatus::COMPLETED,
            'student_id' => $student->id,
            'completion_year' => 2024,
        ]);

        Livewire::test(PublicRepository::class)
            ->set('selectedCategories', ['natural_sciences'])
            ->assertSee('Broiler Performance Under Black Soldier Fly Meal')
            ->assertDontSee('Tomato Supply Chain Efficiency')
            ->set('onlyWithGaps', true)
            ->assertSee('Broiler Performance Under Black Soldier Fly Meal')
            ->assertSee('Omega-3 fatty acid profile enhancement');
    }

    public function test_it_switches_view_mode_between_grid_and_list(): void
    {
        Livewire::test(PublicRepository::class)
            ->assertSet('viewMode', 'grid')
            ->call('setViewMode', 'list')
            ->assertSet('viewMode', 'list');
    }
}
