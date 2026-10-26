<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Livewire\Concerns\ManagesResearchFolder;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ResearchRecord extends Component
{
    use ManagesResearchFolder;

    public Project $project;

    /**
     * Public visitors only see completed studies; authors, supervisors and faculty may also open their own work in progress.
     */
    public function mount(Project $project): void
    {
        if (! $project->status->isPubliclyVisible() && Gate::denies('viewFullContent', $project)) {
            abort(404);
        }

        $this->project = $project;
    }

    public function render(): View
    {
        $project = $this->project->load([
            'student:id,name,department',
            'supervisor:id,name,department',
            'researchGaps.domain:id,name,slug',
        ]);

        $related = Project::query()
            ->publicCatalog()
            ->select(['id', 'title', 'category', 'completion_year', 'student_id'])
            ->with('student:id,name')
            ->where('category', $project->category->value)
            ->whereKeyNot($project->id)
            ->latest('completion_year')
            ->take(4)
            ->get();

        return view('livewire.research-record', [
            'record' => $project,
            'related' => $related,
            'canAccessFullText' => Gate::allows('downloadFullContent', $project),
            'isSaved' => in_array($project->id, $this->savedProjectIds(), true),
        ])->title($project->title);
    }
}
