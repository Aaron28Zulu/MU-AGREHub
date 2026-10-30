<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\ProjectStatus;
use App\Enums\ResearchCategory;
use App\Models\Project;
use App\Models\ResearchDomain;
use App\Models\ResearchGap;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Show the RRIS discovery landing page.
     * Only completed (publicly catalogued) studies are surfaced here.
     */
    public function __invoke(): View
    {
        $recentProjects = Project::query()
            ->publicCatalog()
            ->select(['id', 'title', 'abstract', 'category', 'status', 'completion_year', 'student_id', 'supervisor_id', 'updated_at'])
            ->with(['student:id,name,department', 'supervisor:id,name', 'researchGaps:id,project_id,extracted_gap_text,is_emerging'])
            ->latest('completion_year')
            ->latest('updated_at')
            ->take(4)
            ->get();

        /** @var array<string, int> $categoryCounts */
        $categoryCounts = Project::query()
            ->publicCatalog()
            ->select('category', DB::raw('count(*) as total'))
            ->groupBy('category')
            ->pluck('total', 'category')
            ->toArray();

        $emergingGaps = ResearchGap::query()
            ->where('is_emerging', true)
            ->whereHas('project', fn ($query) => $query->publicCatalog())
            ->with(['domain:id,name', 'project:id,title'])
            ->latest('id')
            ->take(4)
            ->get();

        $totalProjects = Project::query()->publicCatalog()->count();
        $totalGaps = ResearchGap::query()->whereHas('project', fn ($query) => $query->publicCatalog())->count();

        return view('welcome', [
            'recentProjects' => $recentProjects,
            'categories' => ResearchCategory::cases(),
            'categoryCounts' => $categoryCounts,
            'emergingGaps' => $emergingGaps,
            'totalProjects' => $totalProjects,
            'totalGaps' => $totalGaps,
            'totalDomains' => ResearchDomain::query()->has('researchGaps')->count(),
            'yearSpan' => Project::query()->publicCatalog()->selectRaw('min(completion_year) as first_year, max(completion_year) as last_year')->first(),
            'activeStudies' => Project::query()->whereIn('status', [
                ProjectStatus::PROPOSAL_SUBMITTED->value,
                ProjectStatus::UNDER_SUPERVISION->value,
                ProjectStatus::UNDER_REVIEW->value,
            ])->count(),
        ]);
    }
}
