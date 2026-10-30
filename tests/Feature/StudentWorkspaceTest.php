<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\ProjectStatus;
use App\Enums\ResearchCategory;
use App\Enums\UserRole;
use App\Livewire\Student\MilestoneSubmitter;
use App\Livewire\Student\StudentDashboard;
use App\Livewire\Student\SubmissionHistory;
use App\Livewire\StudentSubmissionWizard;
use App\Models\Project;
use App\Models\ProjectManuscript;
use App\Models\SupervisorEvaluation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StudentWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('secure_research');
        Storage::fake('local');

        foreach (UserRole::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
        }
    }

    public function test_guest_is_redirected_to_login_when_accessing_student_workspace(): void
    {
        $response = $this->get(route('student.dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_scenario_a_unenrolled_student_sees_onboarding_wizard_cta(): void
    {
        $student = User::factory()->create(['role' => UserRole::STUDENT]);
        $student->assignRole(UserRole::STUDENT->value);

        $this->actingAs($student);

        Livewire::test(StudentDashboard::class)
            ->assertSee('Initiate Your Postgraduate Research Journey')
            ->assertSee('Register Research Proposal')
            ->assertSee('Dissertation Lifecycle Overview');
    }

    public function test_student_can_register_proposal_via_initiation_wizard(): void
    {
        $student = User::factory()->create(['role' => UserRole::STUDENT]);
        $student->assignRole(UserRole::STUDENT->value);

        $supervisor = User::factory()->create(['name' => 'Dr. C. Mulenga', 'role' => UserRole::SUPERVISOR]);
        $supervisor->assignRole(UserRole::SUPERVISOR->value);

        $this->actingAs($student);

        $proposalFile = UploadedFile::fake()->create('proposal_submission.pdf', 1024, 'application/pdf');

        Livewire::test(StudentSubmissionWizard::class)
            ->assertSee('Institutional Duplication Prevention & Academic Gap Check')
            ->set('title', 'Biochar Soil Amendment in Northern Province Agrosystems')
            ->set('category', ResearchCategory::AGRICULTURE->value)
            ->set('supervisor_id', $supervisor->id)
            ->set('abstract', 'Comprehensive empirical study evaluating biochar formulations on maize nitrogen dynamics over three seasons.')
            ->set('objectives_methodology', 'Objective 1: Measure nitrogen mineralization. Field trials randomized complete block design.')
            ->set('research_gaps', ['Lack of biochar baseline data in acidic northern soils'])
            ->set('full_document', $proposalFile)
            ->call('submit')
            ->assertRedirect(route('student.dashboard'));

        $this->assertDatabaseHas('projects', [
            'student_id' => $student->id,
            'supervisor_id' => $supervisor->id,
            'title' => 'Biochar Soil Amendment in Northern Province Agrosystems',
            'category' => ResearchCategory::AGRICULTURE->value,
            'status' => ProjectStatus::PROPOSAL_SUBMITTED->value,
        ]);

        $this->assertDatabaseHas('project_manuscripts', [
            'version' => 'v1.0',
            'milestone_stage' => 'Proposal',
            'file_name' => 'proposal_submission.pdf',
        ]);
    }

    public function test_scenario_b_active_researcher_under_review_has_locked_submission_zone(): void
    {
        $student = User::factory()->create(['role' => UserRole::STUDENT]);
        $student->assignRole(UserRole::STUDENT->value);

        $supervisor = User::factory()->create(['name' => 'Dr. C. Mulenga', 'role' => UserRole::SUPERVISOR]);
        $supervisor->assignRole(UserRole::SUPERVISOR->value);

        $project = Project::create([
            'title' => 'Cassava Mosaic Resistance Dynamics',
            'abstract' => 'Study on viral resistance phenotypes in cassava.',
            'category' => ResearchCategory::AGRICULTURE,
            'status' => ProjectStatus::UNDER_REVIEW,
            'student_id' => $student->id,
            'supervisor_id' => $supervisor->id,
        ]);

        $this->actingAs($student);

        Livewire::test(StudentDashboard::class)
            ->assertSee('Cassava Mosaic Resistance Dynamics')
            ->assertSee('Dr. C. Mulenga')
            ->assertSee('Current Phase:')
            ->assertSee('Under Review');

        Livewire::test(MilestoneSubmitter::class, ['project' => $project])
            ->assertSee('Manuscript Currently Under Supervisor Review')
            ->assertSee('Dr. C. Mulenga')
            ->assertSee('active submissions are temporarily paused until supervisory feedback')
            ->assertDontSee('Submit Manuscript for Supervisor Review');
    }

    public function test_scenario_b_active_researcher_with_revisions_sees_correction_prompt_and_active_form(): void
    {
        $student = User::factory()->create(['role' => UserRole::STUDENT]);
        $student->assignRole(UserRole::STUDENT->value);

        $supervisor = User::factory()->create(['name' => 'Dr. C. Mulenga', 'role' => UserRole::SUPERVISOR]);
        $supervisor->assignRole(UserRole::SUPERVISOR->value);

        $project = Project::create([
            'title' => 'Drip Irrigation Efficiency in Sweet Potato',
            'abstract' => 'Optimizing emitter spacing for root yield.',
            'category' => ResearchCategory::AGRICULTURE,
            'status' => ProjectStatus::UNDER_SUPERVISION,
            'student_id' => $student->id,
            'supervisor_id' => $supervisor->id,
        ]);

        SupervisorEvaluation::create([
            'project_id' => $project->id,
            'supervisor_id' => $supervisor->id,
            'rubric_score' => 48.0,
            'feedback' => 'Please recalibrate moisture sensors and expand chapter 3 statistical modeling.',
        ]);

        $this->actingAs($student);

        Livewire::test(MilestoneSubmitter::class, ['project' => $project])
            ->assertSee('Corrections & Revisions Requested')
            ->assertSee('Please recalibrate moisture sensors')
            ->assertSee('Submit Revised Draft for Review');

        // Submit revised manuscript
        $revisedFile = UploadedFile::fake()->create('revised_chapter_3.pdf', 2048, 'application/pdf');

        Livewire::test(MilestoneSubmitter::class, ['project' => $project])
            ->set('document', $revisedFile)
            ->set('submission_notes', 'Addressed sensor calibration and updated ANOVA models.')
            ->call('submit')
            ->assertHasNoErrors();

        $project->refresh();
        $this->assertSame(ProjectStatus::UNDER_REVIEW, $project->status);
        $this->assertDatabaseHas('project_manuscripts', [
            'project_id' => $project->id,
            'milestone_stage' => 'Progress Report',
            'file_name' => 'revised_chapter_3.pdf',
        ]);
    }

    public function test_scenario_c_defended_student_sees_archival_and_public_catalog_links(): void
    {
        $student = User::factory()->create(['role' => UserRole::STUDENT]);
        $student->assignRole(UserRole::STUDENT->value);

        $supervisor = User::factory()->create(['name' => 'Dr. C. Mulenga', 'role' => UserRole::SUPERVISOR]);
        $supervisor->assignRole(UserRole::SUPERVISOR->value);

        $project = Project::create([
            'title' => 'Completed Aquaculture Trial on Tilapia Feed',
            'abstract' => 'Optimized insect meal formulations.',
            'category' => ResearchCategory::NATURAL_SCIENCES,
            'status' => ProjectStatus::COMPLETED,
            'student_id' => $student->id,
            'supervisor_id' => $supervisor->id,
            'completion_year' => 2025,
            'full_document_path' => 'manuscripts/2025/tilapia_final.pdf',
        ]);

        $this->actingAs($student);

        Livewire::test(StudentDashboard::class)
            ->assertSee('Research Study Successfully Completed & Defended')
            ->assertSee('View Public Metadata');

        Livewire::test(MilestoneSubmitter::class, ['project' => $project])
            ->assertSee('Research Milestone Pipeline Completed')
            ->assertSee('View in Public Archive');
    }

    public function test_submission_history_lists_iterations_and_evaluations(): void
    {
        $student = User::factory()->create(['role' => UserRole::STUDENT]);
        $student->assignRole(UserRole::STUDENT->value);

        $supervisor = User::factory()->create(['name' => 'Dr. C. Mulenga', 'role' => UserRole::SUPERVISOR]);
        $supervisor->assignRole(UserRole::SUPERVISOR->value);

        $project = Project::create([
            'title' => 'Hydroponic Nutrient Solutions in Greenhouses',
            'abstract' => 'Closed-loop recirculating NFT study.',
            'category' => ResearchCategory::AGRICULTURE,
            'status' => ProjectStatus::UNDER_REVIEW,
            'student_id' => $student->id,
            'supervisor_id' => $supervisor->id,
        ]);

        ProjectManuscript::create([
            'project_id' => $project->id,
            'version' => 'v1.0',
            'milestone_stage' => 'Proposal',
            'file_path' => 'manuscripts/2026/proposal.pdf',
            'file_name' => 'proposal.pdf',
            'file_size' => 1048576,
            'submission_notes' => 'First registered draft.',
        ]);

        ProjectManuscript::create([
            'project_id' => $project->id,
            'version' => 'v1.1',
            'milestone_stage' => 'Progress Report',
            'file_path' => 'manuscripts/2026/progress.pdf',
            'file_name' => 'progress.pdf',
            'file_size' => 2097152,
            'submission_notes' => 'Added preliminary plant tissue data.',
        ]);

        $this->actingAs($student);

        Livewire::test(SubmissionHistory::class)
            ->assertSee('Hydroponic Nutrient Solutions in Greenhouses')
            ->assertSee('v1.0')
            ->assertSee('v1.1')
            ->assertSee('proposal.pdf')
            ->assertSee('progress.pdf')
            ->assertSee('Added preliminary plant tissue data.');
    }

    public function test_manuscript_version_download_authorization(): void
    {
        $student = User::factory()->create(['role' => UserRole::STUDENT]);
        $student->assignRole(UserRole::STUDENT->value);

        $peer = User::factory()->create(['role' => UserRole::STUDENT]);
        $peer->assignRole(UserRole::STUDENT->value);

        $supervisor = User::factory()->create(['role' => UserRole::SUPERVISOR]);
        $supervisor->assignRole(UserRole::SUPERVISOR->value);

        $project = Project::create([
            'title' => 'Silage Quality Under Bacterial Inoculants',
            'abstract' => 'Fermentation kinetics study.',
            'category' => ResearchCategory::NATURAL_SCIENCES,
            'status' => ProjectStatus::UNDER_REVIEW,
            'student_id' => $student->id,
            'supervisor_id' => $supervisor->id,
        ]);

        Storage::disk('secure_research')->put('manuscripts/2026/silage_v1.pdf', 'Dummy PDF Content');

        $manuscript = ProjectManuscript::create([
            'project_id' => $project->id,
            'version' => 'v1.0',
            'milestone_stage' => 'Proposal',
            'file_path' => 'manuscripts/2026/silage_v1.pdf',
            'file_name' => 'silage_v1.pdf',
            'file_size' => 1024,
        ]);

        // Student owner can download their version
        $response = $this->actingAs($student)->get(route('projects.manuscripts.download', [
            'project' => $project->id,
            'manuscript' => $manuscript->id,
        ]));
        $response->assertOk();

        // Peer student receives 403 Forbidden
        $responsePeer = $this->actingAs($peer)->get(route('projects.manuscripts.download', [
            'project' => $project->id,
            'manuscript' => $manuscript->id,
        ]));
        $responsePeer->assertForbidden();
    }
}
