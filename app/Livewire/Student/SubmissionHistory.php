<?php

declare(strict_types=1);

namespace App\Livewire\Student;

use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class SubmissionHistory extends Component
{
    public ?Project $project = null;

    public function mount(): void
    {
        $this->project = Project::query()
            ->with([
                'supervisor:id,name,email,department',
                'manuscripts' => fn ($query) => $query->latest(),
                'evaluations' => fn ($query) => $query->with('supervisor:id,name')->latest(),
            ])
            ->where('student_id', Auth::id())
            ->latest()
            ->first();
    }

    public function render(): View
    {
        return view('livewire.student.submission-history', [
            'manuscripts' => $this->project ? $this->project->manuscripts : collect(),
            'evaluations' => $this->project ? $this->project->evaluations : collect(),
        ]);
    }
}
