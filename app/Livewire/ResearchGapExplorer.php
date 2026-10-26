<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\ResearchDomain;
use App\Models\ResearchGap;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ResearchGapExplorer extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'domain')]
    public string $domain = '';

    #[Url(as: 'signal')]
    public string $signal = 'all';

    public function updating(string $property): void
    {
        if (in_array($property, ['search', 'domain', 'signal'], true)) {
            $this->resetPage();
        }
    }

    public function selectDomain(string $slug): void
    {
        $this->domain = $this->domain === $slug ? '' : $slug;
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'domain']);
        $this->signal = 'all';
        $this->resetPage();
    }

    public function render(): View
    {
        // Only gaps derived from publicly catalogued (completed) studies are exposed.
        $publicGaps = fn (Builder $query): Builder => $query->whereHas('project', fn (Builder $q) => $q->publicCatalog());

        $domains = ResearchDomain::query()
            ->withCount([
                'researchGaps as gaps_count' => $publicGaps,
                'researchGaps as emerging_count' => fn (Builder $q) => $publicGaps($q)->where('is_emerging', true),
                'researchGaps as saturated_count' => fn (Builder $q) => $publicGaps($q)->where('is_saturated', true),
            ])
            ->orderByDesc('gaps_count')
            ->orderBy('name')
            ->get()
            ->filter(fn (ResearchDomain $domain): bool => $domain->gaps_count > 0)
            ->values();

        $gaps = $publicGaps(ResearchGap::query())
            ->with(['domain:id,name,slug', 'project:id,title,category,completion_year'])
            ->when($this->search !== '', fn (Builder $q) => $q->where('extracted_gap_text', 'like', '%'.trim($this->search).'%'))
            ->when($this->domain !== '', fn (Builder $q) => $q->whereHas('domain', fn (Builder $d) => $d->where('slug', $this->domain)))
            ->when($this->signal === 'emerging', fn (Builder $q) => $q->where('is_emerging', true))
            ->when($this->signal === 'saturated', fn (Builder $q) => $q->where('is_saturated', true))
            ->latest('id')
            ->paginate(12);

        return view('livewire.research-gap-explorer', [
            'domains' => $domains,
            'gaps' => $gaps,
            'activeDomain' => $domains->firstWhere('slug', $this->domain),
            'maxDomainCount' => max(1, (int) $domains->max('gaps_count')),
        ])->title('Research gaps');
    }
}
