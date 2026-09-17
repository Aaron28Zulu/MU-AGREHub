<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\ProjectStatus;
use App\Enums\ResearchCategory;
use App\Enums\UserRole;
use App\Models\Project;
use App\Models\SupervisorEvaluation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AcademicIntegrityTest extends TestCase
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

    public function test_guest_cannot_download_restricted_manuscript(): void
    {
        $student = User::factory()->create(['role' => UserRole::STUDENT]);
        $student->assignRole(UserRole::STUDENT->value);

        Storage::disk('secure_research')->put('manuscripts/test.pdf', 'Confidential Research Data');

        $project = Project::create([
            'title' => 'Soil Fertility Evaluation',
            'abstract' => 'Evaluating biochar and microbial respiration across smallholder plots.',
            'category' => ResearchCategory::CROP_SCIENCE,
            'status' => ProjectStatus::COMPLETED,
            'student_id' => $student->id,
            'full_document_path' => 'manuscripts/test.pdf',
        ]);

        $response = $this->get(route('projects.download', $project));
        $response->assertRedirect(route('login'));
    }

    public function test_ordinary_student_peer_cannot_download_another_students_manuscript(): void
    {
        $authorStudent = User::factory()->create(['role' => UserRole::STUDENT]);
        $authorStudent->assignRole(UserRole::STUDENT->value);

        $peerStudent = User::factory()->create(['role' => UserRole::STUDENT]);
        $peerStudent->assignRole(UserRole::STUDENT->value);

        Storage::disk('secure_research')->put('manuscripts/test.pdf', 'Confidential Research Data');

        $project = Project::create([
            'title' => 'Soil Fertility Evaluation',
            'abstract' => 'Evaluating biochar and microbial respiration across smallholder plots.',
            'category' => ResearchCategory::CROP_SCIENCE,
            'status' => ProjectStatus::COMPLETED,
            'student_id' => $authorStudent->id,
            'full_document_path' => 'manuscripts/test.pdf',
        ]);

        $response = $this->actingAs($peerStudent)->get(route('projects.download', $project));
        $response->assertForbidden();
    }

    public function test_student_author_can_download_their_own_manuscript(): void
    {
        $authorStudent = User::factory()->create(['role' => UserRole::STUDENT]);
        $authorStudent->assignRole(UserRole::STUDENT->value);

        Storage::disk('secure_research')->put('manuscripts/my_draft.pdf', 'Confidential Research Data');

        $project = Project::create([
            'title' => 'Soil Fertility Evaluation',
            'abstract' => 'Evaluating biochar and microbial respiration across smallholder plots.',
            'category' => ResearchCategory::CROP_SCIENCE,
            'status' => ProjectStatus::UNDER_SUPERVISION,
            'student_id' => $authorStudent->id,
            'full_document_path' => 'manuscripts/my_draft.pdf',
        ]);

        $response = $this->actingAs($authorStudent)->get(route('projects.download', $project));
        $response->assertOk();
    }

    public function test_assigned_supervisor_can_download_assigned_project_manuscript(): void
    {
        $student = User::factory()->create(['role' => UserRole::STUDENT]);
        $student->assignRole(UserRole::STUDENT->value);

        $supervisor = User::factory()->create(['role' => UserRole::SUPERVISOR]);
        $supervisor->assignRole(UserRole::SUPERVISOR->value);

        Storage::disk('secure_research')->put('manuscripts/review.pdf', 'Draft manuscript content');

        $project = Project::create([
            'title' => 'Soil Fertility Evaluation',
            'abstract' => 'Evaluating biochar and microbial respiration across smallholder plots.',
            'category' => ResearchCategory::CROP_SCIENCE,
            'status' => ProjectStatus::UNDER_REVIEW,
            'student_id' => $student->id,
            'supervisor_id' => $supervisor->id,
            'full_document_path' => 'manuscripts/review.pdf',
        ]);

        $response = $this->actingAs($supervisor)->get(route('projects.download', $project));
        $response->assertOk();
    }

    public function test_unrelated_student_cannot_download_supervisor_marked_up_evaluations(): void
    {
        $author = User::factory()->create(['role' => UserRole::STUDENT]);
        $author->assignRole(UserRole::STUDENT->value);

        $supervisor = User::factory()->create(['role' => UserRole::SUPERVISOR]);
        $supervisor->assignRole(UserRole::SUPERVISOR->value);

        $unrelatedStudent = User::factory()->create(['role' => UserRole::STUDENT]);
        $unrelatedStudent->assignRole(UserRole::STUDENT->value);

        Storage::disk('secure_research')->put('evaluations/markup.pdf', 'Confidential Supervisor Marks');

        $project = Project::create([
            'title' => 'Soil Fertility Evaluation',
            'abstract' => 'Evaluating biochar and microbial respiration.',
            'category' => ResearchCategory::CROP_SCIENCE,
            'status' => ProjectStatus::UNDER_SUPERVISION,
            'student_id' => $author->id,
            'supervisor_id' => $supervisor->id,
        ]);

        SupervisorEvaluation::create([
            'project_id' => $project->id,
            'supervisor_id' => $supervisor->id,
            'rubric_score' => 78.5,
            'feedback' => 'Detailed corrections required on Chapter 3.',
            'document_markup_path' => 'evaluations/markup.pdf',
        ]);

        // Unrelated student should be denied
        $response = $this->actingAs($unrelatedStudent)->get(route('projects.download.marked-up', $project));
        $response->assertForbidden();

        // Project owner author can download their own markup
        $authorResponse = $this->actingAs($author)->get(route('projects.download.marked-up', $project));
        $authorResponse->assertOk();
    }

    public function test_guest_accessing_student_dashboard_is_redirected_to_login(): void
    {
        $response = $this->get(route('student.dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_public_catalog_is_accessible_to_unauthenticated_guests(): void
    {
        $response = $this->get(route('catalog'));
        $response->assertOk();
    }
}
