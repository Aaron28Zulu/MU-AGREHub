<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\ProjectStatus;
use App\Enums\ResearchCategory;
use App\Models\Project;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class PublicRepository extends Component
{
    use WithPagination;

    #[Url(as: 'search')]
    public string $search = '';

    /**
     * @var array<string>
     */
    #[Url(as: 'categories')]
    public array $selectedCategories = [];

    /**
     * @var array<int|string>
     */
    #[Url(as: 'years')]
    public array $selectedYears = [];

    #[Url(as: 'gaps_only')]
    public bool $onlyWithGaps = false;

    #[Url(as: 'sort')]
    public string $sort = 'latest';

    #[Url(as: 'view')]
    public string $viewMode = 'grid';

    /**
     * Initialize parameters and adapt incoming legacy query params (e.g. ?category=crop_science).
     */
    public function mount(): void
    {
        if (request()->has('category') && empty($this->selectedCategories)) {
            $cat = request()->query('category');
            $this->selectedCategories = is_array($cat) ? $cat : [$cat];
        }

        if (request()->has('year') && empty($this->selectedYears)) {
            $yr = request()->query('year');
            $this->selectedYears = is_array($yr) ? $yr : [$yr];
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedCategories(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedYears(): void
    {
        $this->resetPage();
    }

    public function updatingOnlyWithGaps(): void
    {
        $this->resetPage();
    }

    public function updatingSort(): void
    {
        $this->resetPage();
    }

    /**
     * Remove a single category from the active filter list.
     */
    public function removeCategory(string $category): void
    {
        $this->selectedCategories = array_values(
            array_filter($this->selectedCategories, fn ($c) => $c !== $category)
        );
        $this->resetPage();
    }

    /**
     * Remove a single year from the active filter list.
     */
    public function removeYear(int|string $year): void
    {
        $this->selectedYears = array_values(
            array_filter($this->selectedYears, fn ($y) => (string) $y !== (string) $year)
        );
        $this->resetPage();
    }

    /**
     * Toggle the gap-only filter.
     */
    public function toggleOnlyWithGaps(): void
    {
        $this->onlyWithGaps = ! $this->onlyWithGaps;
        $this->resetPage();
    }

    /**
     * Switch view display between grid and list.
     */
    public function setViewMode(string $mode): void
    {
        $this->viewMode = in_array($mode, ['grid', 'list'], true) ? $mode : 'grid';
    }

    /**
     * Reset all active search terms, facets, and pagination.
     */
    public function resetFilters(): void
    {
        $this->reset(['search', 'selectedCategories', 'selectedYears', 'onlyWithGaps']);
        $this->sort = 'latest';
        $this->resetPage();
    }

    /**
     * Count completed projects per research category.
     *
     * @return array<string, int>
     */
    protected function getCategoryCounts(): array
    {
        /** @var array<string, int> $counts */
        $counts = Project::query()
            ->where('status', ProjectStatus::COMPLETED->value)
            ->select('category', DB::raw('count(*) as count'))
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();

        return $counts;
    }

    /**
     * Get distinct completion years from cataloged records.
     *
     * @return list<int>
     */
    protected function getAvailableYears(): array
    {
        $years = Project::query()
            ->where('status', ProjectStatus::COMPLETED->value)
            ->whereNotNull('completion_year')
            ->distinct()
            ->orderByDesc('completion_year')
            ->pluck('completion_year')
            ->map(fn ($y) => (int) $y)
            ->toArray();

        if (empty($years)) {
            return [(int) date('Y'), (int) date('Y') - 1, (int) date('Y') - 2];
        }

        return $years;
    }

    /**
     * Render the 2-column faceted repository view.
     */
    public function render(ProjectRepositoryInterface $repository): View
    {
        $projects = $repository->getPublicCatalog([
            'search' => $this->search,
            'categories' => $this->selectedCategories,
            'years' => $this->selectedYears,
            'only_with_gaps' => $this->onlyWithGaps,
            'sort' => $this->sort,
            'per_page' => 10,
        ]);

        $totalCatalogCount = Project::query()
            ->where('status', ProjectStatus::COMPLETED->value)
            ->count();

        $categoryCounts = $this->getCategoryCounts();
        $availableYears = $this->getAvailableYears();

        return view('livewire.public-repository', [
            'projects' => $projects,
            'totalCatalogCount' => $totalCatalogCount,
            'categories' => ResearchCategory::cases(),
            'categoryCounts' => $categoryCounts,
            'availableYears' => $availableYears,
        ]);
    }
}
