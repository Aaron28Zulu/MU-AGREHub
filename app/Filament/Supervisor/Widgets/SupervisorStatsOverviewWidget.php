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
     * Office members without supervision duties have no review queue.
     */
    public static function canView(): bool
    {
        return auth()->user()?->isFaculty() ?? false;
    }

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
            Stat::make('Advisees', (string) $assignedCount)
                ->description('Studies you supervise')
                ->icon('heroicon-o-user-group'),

            Stat::make('Awaiting review', (string) $awaitingReviewCount)
                ->description('Proposals and drafts')
                ->icon('heroicon-o-clock')
                ->color($awaitingReviewCount > 0 ? 'warning' : 'gray'),

            Stat::make('Similarity alerts', (string) $similarityAlertsCount)
                ->description('Scored 20% or higher')
                ->icon('heroicon-o-exclamation-triangle')
                ->color($similarityAlertsCount > 0 ? 'danger' : 'gray'),

            Stat::make('Completed', (string) $completedCount)
                ->description('Defended and published')
                ->icon('heroicon-o-check-badge'),
        ];
    }
}
