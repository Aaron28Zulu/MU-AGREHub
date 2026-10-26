<?php

declare(strict_types=1);

namespace App\Filament\Supervisor\Resources\SupervisorProjectResource\Pages;

use App\Enums\ProjectStatus;
use App\Filament\Supervisor\Resources\SupervisorProjectResource;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListSupervisorProjects extends ListRecords
{
    protected static string $resource = SupervisorProjectResource::class;

    protected ?string $subheading = 'Studies you supervise. Open one to read the manuscript, run an originality check and record a decision.';

    protected function getHeaderActions(): array
    {
        return [];
    }

    /**
     * @return array<string, Tab>
     */
    public function getTabs(): array
    {
        $count = fn (array $statuses): int => SupervisorProjectResource::getModel()::query()
            ->where('supervisor_id', Auth::id())
            ->whereIn('status', $statuses)
            ->count();

        $awaiting = [ProjectStatus::PROPOSAL_SUBMITTED->value, ProjectStatus::UNDER_REVIEW->value];
        $active = [ProjectStatus::DRAFT->value, ProjectStatus::UNDER_SUPERVISION->value];

        return [
            'all' => Tab::make('All advisees'),
            'awaiting' => Tab::make('Needs review')
                ->icon('heroicon-m-inbox-arrow-down')
                ->badge($count($awaiting) ?: null)
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->whereIn('status', $awaiting)),
            'active' => Tab::make('In progress')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->whereIn('status', $active)),
            'completed' => Tab::make('Completed')
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', ProjectStatus::COMPLETED->value)),
        ];
    }
}
