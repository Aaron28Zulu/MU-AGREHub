<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\ProjectStatus;
use App\Enums\ResearchCategory;
use App\Enums\UserRole;
use App\Jobs\AnalyzeProjectGapsJob;
use App\Models\Project;
use App\Models\ResearchDomain;
use App\Models\ResearchGap;
use App\Models\User;
use App\Repositories\GapAggregationRepository;
use App\Services\ResearchGapAnalysisService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ResearchGapAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (UserRole::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
        }
    }

    public function test_process_research_gaps_command_dispatches_jobs_for_completed_projects(): void
    {
        Queue::fake();

        $student = User::factory()->create(['role' => UserRole::STUDENT]);

        Project::create([
            'title' => 'Completed Soil Study',
            'abstract' => 'Comprehensive soil fertility evaluation.',
            'category' => ResearchCategory::CROP_SCIENCE,
            'status' => ProjectStatus::COMPLETED,
            'student_id' => $student->id,
        ]);

        $this->artisan('agrihub:process-gaps')
            ->assertSuccessful();

        Queue::assertPushed(AnalyzeProjectGapsJob::class, 1);
    }

    public function test_research_gap_service_creates_domains_and_gaps(): void
    {
        $student = User::factory()->create(['role' => UserRole::STUDENT]);

        $project = Project::create([
            'title' => 'Formulation of Black Soldier Fly Larval Protein',
            'abstract' => 'Evaluation of insect meal as a replacement for conventional poultry fishmeal.',
            'category' => ResearchCategory::ANIMAL_SCIENCE,
            'status' => ProjectStatus::COMPLETED,
            'student_id' => $student->id,
        ]);

        /** @var ResearchGapAnalysisService $service */
        $service = app(ResearchGapAnalysisService::class);
        $gaps = $service->analyzeCompletedProject($project);

        $this->assertNotEmpty($gaps);
        $this->assertDatabaseHas('research_gaps', [
            'project_id' => $project->id,
        ]);
        $this->assertDatabaseCount('research_domains', 1);
    }

    public function test_gap_aggregation_repository_identifies_emerging_opportunities(): void
    {
        $domain = ResearchDomain::create([
            'name' => 'Precision Irrigation',
            'slug' => 'precision-irrigation',
        ]);

        $student = User::factory()->create(['role' => UserRole::STUDENT]);
        $project = Project::create([
            'title' => 'Solar Drip Irrigation Analysis',
            'abstract' => 'Sensor-actuated irrigation.',
            'category' => ResearchCategory::CROP_SCIENCE,
            'status' => ProjectStatus::COMPLETED,
            'student_id' => $student->id,
        ]);

        ResearchGap::create([
            'project_id' => $project->id,
            'domain_id' => $domain->id,
            'extracted_gap_text' => 'Micro-dosing nutrient delivery via solar pumps in alkaline soils.',
            'is_saturated' => false,
            'is_emerging' => true,
        ]);

        /** @var GapAggregationRepository $repository */
        $repository = app(GapAggregationRepository::class);
        $emerging = $repository->getEmergingOpportunities();

        $this->assertCount(1, $emerging);
        $this->assertEquals('Precision Irrigation', $emerging->first()->domain->name);
    }
}
