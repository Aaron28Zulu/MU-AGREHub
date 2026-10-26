<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\EntryStatus;
use App\Enums\PlotStatus;
use App\Filament\Resources\AssociationEventResource;
use App\Filament\Resources\ChallengeEntryResource;
use App\Filament\Resources\CropPlotResource;
use App\Models\AssociationEvent;
use App\Models\ChallengeEntry;
use App\Models\CropPlot;
use App\Models\PlotObservation;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * At-a-glance status of the Office of Research and Innovation's programmes.
 */
class ResearchOfficeStatsWidget extends BaseWidget
{
    protected static ?int $sort = 0;

    protected ?string $heading = 'Research & Innovation Office';

    public static function canView(): bool
    {
        return auth()->user()?->managesResearchOffice() ?? false;
    }

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        $nextEvent = AssociationEvent::query()->upcoming()->first();

        return [
            Stat::make('Entries awaiting a decision', ChallengeEntry::query()->where('status', EntryStatus::SUBMITTED->value)->count())
                ->description(ChallengeEntry::query()->where('status', EntryStatus::SHORTLISTED->value)->count().' shortlisted')
                ->icon('heroicon-o-trophy')
                ->url(ChallengeEntryResource::getUrl('index')),
            Stat::make('Upcoming events', AssociationEvent::query()->where('starts_at', '>=', now())->count())
                ->description($nextEvent ? 'Next: '.$nextEvent->title.', '.$nextEvent->starts_at->format('j M') : 'Nothing scheduled')
                ->icon('heroicon-o-megaphone')
                ->url(AssociationEventResource::getUrl('index')),
            Stat::make('Plots in the field', CropPlot::query()->where('status', PlotStatus::GROWING->value)->count())
                ->description(PlotObservation::query()->where('observed_on', '>=', now()->subDays(30))->count().' field records in the last 30 days')
                ->icon('heroicon-o-sun')
                ->url(CropPlotResource::getUrl('index')),
        ];
    }
}
