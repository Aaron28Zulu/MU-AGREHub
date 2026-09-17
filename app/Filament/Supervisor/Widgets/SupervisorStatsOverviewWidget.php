<?php

declare(strict_types=1);

namespace App\Filament\Supervisor\Widgets;

use App\Enums\ProjectStatus;
use App\Models\Project;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class SupervisorStatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    /**
     * Build the stats overview cards strictly scoped to the authenticated supervisor.
     *
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        $supervisorId = Auth::id();

        // 1. Total assigned projects / students
        $assignedCount = Project::query()
            ->where('supervisor_id', $supervisorId)
            ->count();

        // 2. Assigned projects awaiting review (proposal_submitted or under_review)
        $awaitingReviewCount = Project::query()
            ->where('supervisor_id', $supervisorId)
            ->whereIn('status', [
                ProjectStatus::PROPOSAL_SUBMITTED->value,
                ProjectStatus::UNDER_REVIEW->value,
            ])
            ->count();

        // 3. Similarity Alerts (similarity score >= 20%)
        $similarityAlertsCount = Project::query()
            ->where('supervisor_id', $supervisorId)
            ->whereHas('latestSimilarityReport', function ($query): void {
                $query->where('similarity_score', '>=', 20.0);
            })
            ->count();

        // 4. Completed & Defended
        $completedCount = Project::query()
            ->where('supervisor_id', $supervisorId)
            ->where('status', ProjectStatus::COMPLETED->value)
            ->count();

        return [
            Stat::make('Assigned Supervisees', (string) $assignedCount)
                ->description('Total active research advisees')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),

            Stat::make('Submissions Awaiting Review', (string) $awaitingReviewCount)
                ->description('Proposals & drafts pending grading')
                ->descriptionIcon('heroicon-m-clock')
                ->color($awaitingReviewCount > 0 ? 'warning' : 'gray'),

            Stat::make('Similarity Alerts', (string) $similarityAlertsCount)
                ->description('Flagged submissions (≥ 20% index)')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($similarityAlertsCount > 0 ? 'danger' : 'success'),

            Stat::make('Completed & Defended', (string) $completedCount)
                ->description('Successfully passed & published')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
        ];
    }
}
