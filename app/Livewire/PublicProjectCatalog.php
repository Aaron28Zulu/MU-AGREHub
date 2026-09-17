<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\ResearchCategory;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class PublicProjectCatalog extends Component
{
    use WithPagination;

    public string $search = '';

    public string $category = '';

    public ?int $year = null;

    /**
     * Reset pagination when search query is updated.
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Reset pagination when category filter is updated.
     */
    public function updatingCategory(): void
    {
        $this->resetPage();
    }

    /**
     * Render the public catalog view.
     */
    public function render(ProjectRepositoryInterface $repository): View
    {
        $projects = $repository->getPublicCatalog([
            'search' => $this->search,
            'category' => $this->category,
            'year' => $this->year,
            'per_page' => 12,
        ]);

        return view('livewire.public-project-catalog', [
            'projects' => $projects,
            'categories' => ResearchCategory::cases(),
        ]);
    }
}
