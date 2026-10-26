<?php

declare(strict_types=1);

namespace App\Livewire\Innovation;

use App\Enums\ChallengeStatus;
use App\Enums\EntryStatus;
use App\Enums\PlotStatus;
use App\Models\AssociationEvent;
use App\Models\ChallengeEntry;
use App\Models\CropPlot;
use App\Models\InnovationChallenge;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * Public front page of the MUSAGRICA Office of Research and Innovation: the current
 * innovation challenge, upcoming debates and workshops, and the crop production plots.
 */
#[Layout('layouts.app')]
class InnovationHub extends Component
{
    public function render(): View
    {
        $challenges = InnovationChallenge::query()
            ->published()
            ->withCount('entries')
            ->orderByRaw('case when status = ? then 0 when status = ? then 1 else 2 end', [ChallengeStatus::OPEN->value, ChallengeStatus::REVIEWING->value])
            ->latest('closes_on')
            ->get();

        $recognised = ChallengeEntry::query()
            ->where('status', EntryStatus::RECOGNISED->value)
            ->whereHas('challenge', fn ($query) => $query->published())
            ->with(['student:id,name', 'challenge:id,edition'])
            ->latest('updated_at')
            ->take(4)
            ->get();

        return view('livewire.innovation.innovation-hub', [
            'featuredChallenge' => $challenges->first(),
            'pastChallenges' => $challenges->slice(1),
            'upcomingEvents' => AssociationEvent::query()->published()->upcoming()->withCount('attendees')->take(4)->get(),
            'pastEvents' => AssociationEvent::query()->published()->where('starts_at', '<', now()->startOfDay())->latest('starts_at')->take(3)->get(),
            'plots' => CropPlot::query()
                ->withCount('observations')
                ->orderByRaw('case when status = ? then 0 when status = ? then 1 else 2 end', [PlotStatus::GROWING->value, PlotStatus::PREPARING->value])
                ->latest('planted_on')
                ->take(6)
                ->get(),
            'recognised' => $recognised,
        ])->title('Research & Innovation');
    }
}
