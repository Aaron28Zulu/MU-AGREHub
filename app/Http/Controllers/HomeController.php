<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\ProjectStatus;
use App\Enums\ResearchCategory;
use App\Models\Project;
use App\Models\ResearchGap;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Show the AgriHub academic landing page.
     */
    public function __invoke(): View
    {
        $recentProjects = Project::query()
            ->with(['student', 'supervisor', 'researchGaps'])
            ->where(function ($query): void {
                $query->where('status', ProjectStatus::COMPLETED)
                    ->orWhereNotNull('abstract');
            })
            ->latest('updated_at')
            ->take(3)
            ->get();

        $categories = ResearchCategory::cases();
        $totalProjects = Project::count();
        $totalGaps = ResearchGap::count();

        return view('welcome', [
            'recentProjects' => $recentProjects,
            'categories' => $categories,
            'totalProjects' => $totalProjects,
            'totalGaps' => $totalGaps,
        ]);
    }
}
