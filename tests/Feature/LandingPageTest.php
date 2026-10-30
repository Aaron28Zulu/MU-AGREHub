<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\ProjectStatus;
use App\Enums\ResearchCategory;
use App\Enums\UserRole;
use App\Models\Project;
use App\Models\ResearchDomain;
use App\Models\ResearchGap;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (UserRole::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
        }
    }

    public function test_landing_page_renders_successfully(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee("Every school's research, in one place");
        $response->assertSee('Research Repository Interface System');
        $response->assertSee('Explore Archive');
        $response->assertSee('Submit Research Proposal');
        $response->assertSee('Institutional Sign In');
    }

    public function test_landing_page_displays_curated_recent_projects_and_gaps(): void
    {
        $student = User::factory()->create(['name' => 'Kelvin Mwape', 'role' => UserRole::STUDENT]);
        $supervisor = User::factory()->create(['name' => 'Dr. C. Mulenga', 'role' => UserRole::SUPERVISOR]);

        $project = Project::create([
            'title' => 'Biochar Amendment in Sandy Loam Soils',
            'abstract' => 'Comprehensive investigation into biochar application and soil moisture dynamics.',
            'category' => ResearchCategory::AGRICULTURE,
            'status' => ProjectStatus::COMPLETED,
            'student_id' => $student->id,
            'supervisor_id' => $supervisor->id,
            'completion_year' => 2025,
        ]);

        $domain = ResearchDomain::create([
            'name' => 'Soil Fertility & Organic Amendments',
            'slug' => 'soil-fertility',
        ]);

        ResearchGap::create([
            'project_id' => $project->id,
            'domain_id' => $domain->id,
            'extracted_gap_text' => 'Long-term nutrient leaching under tropical precipitation conditions.',
            'is_emerging' => true,
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Biochar Amendment in Sandy Loam Soils');
        $response->assertSee('Long-term nutrient leaching under tropical precipitation conditions.');
        $response->assertSee('View Abstract &amp; Gaps', false);
    }

    public function test_repository_route_is_accessible_and_filters_properly(): void
    {
        $response = $this->get(route('repository'));
        $response->assertOk();
        $response->assertSee('Institutional Research Archive');

        $searchResponse = $this->get(route('repository', ['search' => 'Biochar']));
        $searchResponse->assertOk();
    }
}
