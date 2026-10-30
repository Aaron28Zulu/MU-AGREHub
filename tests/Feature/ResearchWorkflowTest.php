<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\ProjectStatus;
use App\Enums\ResearchCategory;
use App\Enums\UserRole;
use App\Filament\Supervisor\Resources\SupervisorProjectResource\Pages\ViewSupervisorProject;
use App\Jobs\AnalyzeProjectGapsJob;
use App\Jobs\RunSimilarityAnalysisJob;
use App\Livewire\Student\MilestoneSubmitter;
use App\Livewire\Student\StudentDashboard;
use App\Livewire\StudentSubmissionWizard;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ResearchWorkflowTest extends TestCase
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

    protected function makeProject(ProjectStatus $status): Project
    {
        return Project::create([
            'title' => 'Groundnut Rosette Virus Tolerance',
            'abstract' => 'Screening groundnut lines for tolerance to rosette virus under field conditions.',
            'category' => ResearchCategory::ENVIRONMENTAL_SCIENCE,
            'status' => $status,
            'student_id' => $this->student->id,
            'supervisor_id' => $this->supervisor->id,
        ]);
    }

    public function test_approving_a_proposal_opens_supervision_instead_of_completing_the_study(): void
    {
        Queue::fake([AnalyzeProjectGapsJob::class, RunSimilarityAnalysisJob::class]);
        $project = $this->makeProject(ProjectStatus::PROPOSAL_SUBMITTED);

        $this->actingAs($this->supervisor);

        Livewire::test(ViewSupervisorProject::class, ['record' => $project->getKey()])
            ->callAction('review_submission', [
                'rubric_score' => 70,
                'decision' => 'approve',
                'feedback' => 'Sound proposal, proceed to fieldwork.',
            ])
            ->assertHasNoActionErrors();

        $this->assertSame(ProjectStatus::UNDER_SUPERVISION, $project->refresh()->status);
        $this->assertNull($project->completion_year);
        Queue::assertNotPushed(AnalyzeProjectGapsJob::class);
    }

    public function test_accepting_a_progress_milestone_keeps_the_study_under_supervision(): void
    {
        Queue::fake([AnalyzeProjectGapsJob::class, RunSimilarityAnalysisJob::class]);
        $project = $this->makeProject(ProjectStatus::UNDER_REVIEW);

        $this->actingAs($this->supervisor);

        Livewire::test(ViewSupervisorProject::class, ['record' => $project->getKey()])
            ->callAction('review_submission', [
                'rubric_score' => 64,
                'decision' => 'continue',
                'feedback' => 'Good progress report.',
            ])
            ->assertHasNoActionErrors();

        $this->assertSame(ProjectStatus::UNDER_SUPERVISION, $project->refresh()->status);
    }

    public function test_final_approval_queues_research_gap_analysis_and_notifies_the_student(): void
    {
        Queue::fake([AnalyzeProjectGapsJob::class, RunSimilarityAnalysisJob::class]);
        $project = $this->makeProject(ProjectStatus::UNDER_REVIEW);

        $this->actingAs($this->supervisor);

        Livewire::test(ViewSupervisorProject::class, ['record' => $project->getKey()])
            ->callAction('review_submission', [
                'rubric_score' => 82,
                'decision' => 'approve',
                'feedback' => 'Ready for the archive.',
            ])
            ->assertHasNoActionErrors();

        $this->assertSame(ProjectStatus::COMPLETED, $project->refresh()->status);
        Queue::assertPushed(AnalyzeProjectGapsJob::class, fn (AnalyzeProjectGapsJob $job): bool => $job->project->is($project));
        $this->assertSame(1, $this->student->notifications()->count());

        $this->actingAs($this->student);

        Livewire::test(StudentDashboard::class)
            ->assertSee('Research Milestone Evaluated')
            ->call('markNotificationsRead');

        $this->assertSame(0, $this->student->unreadNotifications()->count());
    }

    public function test_milestone_upload_queues_similarity_check_and_alerts_supervisor(): void
    {
        Queue::fake([AnalyzeProjectGapsJob::class, RunSimilarityAnalysisJob::class]);
        Storage::fake('secure_research');
        $project = $this->makeProject(ProjectStatus::UNDER_SUPERVISION);

        $this->actingAs($this->student);

        Livewire::test(MilestoneSubmitter::class, ['project' => $project])
            ->set('document', UploadedFile::fake()->create('chapter-three.pdf', 120, 'application/pdf'))
            ->call('submit')
            ->assertHasNoErrors();

        $this->assertSame(ProjectStatus::UNDER_REVIEW, $project->refresh()->status);
        Queue::assertPushed(RunSimilarityAnalysisJob::class);
        $this->assertSame('New submission awaiting review', $this->supervisor->notifications()->first()?->data['title']);
    }

    public function test_students_with_an_active_study_are_sent_back_to_their_workspace(): void
    {
        $this->makeProject(ProjectStatus::UNDER_SUPERVISION);

        $this->actingAs($this->student);

        Livewire::test(StudentSubmissionWizard::class)
            ->assertRedirect(route('student.dashboard'));
    }

    public function test_proposal_wizard_flags_related_completed_studies(): void
    {
        Project::create([
            'title' => 'Groundnut Aflatoxin Contamination in Storage',
            'abstract' => 'Assessing aflatoxin levels in stored groundnuts.',
            'category' => ResearchCategory::ECONOMICS,
            'status' => ProjectStatus::COMPLETED,
            'student_id' => $this->supervisor->id,
            'completion_year' => 2024,
        ]);

        $this->actingAs($this->student);

        Livewire::test(StudentSubmissionWizard::class)
            ->set('title', 'Reducing groundnut losses on smallholder farms')
            ->assertSee('Groundnut Aflatoxin Contamination in Storage');
    }

    public function test_demo_quick_login_is_unavailable_outside_local_development(): void
    {
        $this->app->detectEnvironment(fn (): string => 'production');

        $this->get(route('login.quick', ['email' => $this->student->email]))->assertNotFound();
        $this->assertGuest();
    }

    public function test_faculty_sign_in_lands_in_the_lecturer_portal(): void
    {
        $dean = User::factory()->create(['role' => UserRole::DEAN, 'password' => 'secret-pass']);
        $dean->assignRole(UserRole::DEAN->value);

        $this->post(route('login.attempt'), ['email' => $dean->email, 'password' => 'secret-pass'])
            ->assertRedirect('/supervisor');
    }
}
