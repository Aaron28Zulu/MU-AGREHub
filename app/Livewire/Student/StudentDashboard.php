<?php

declare(strict_types=1);

namespace App\Livewire\Student;

use App\Enums\ProjectStatus;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class StudentDashboard extends Component
{
    public ?Project $project = null;

    public function mount(): void
    {
        $this->loadStudentProject();
    }

    /**
     * Refresh project data when milestone is submitted by child component.
     */
    #[On('milestone-submitted')]
    public function loadStudentProject(): void
    {
        $this->project = Project::query()
            ->with([
                'supervisor:id,name,email,department',
                'evaluations' => fn ($query) => $query->with('supervisor:id,name,email,department')->latest(),
                'manuscripts' => fn ($query) => $query->latest(),
                'latestSimilarityReport',
                'researchGaps',
            ])
            ->where('student_id', Auth::id())
            ->latest()
            ->first();
    }

    /**
     * Determine numerical index of the current project status for pipeline progress calculation.
     */
    public function getPipelineStepIndex(): int
    {
        if (! $this->project) {
            return 0;
        }

        return match ($this->project->status) {
            ProjectStatus::DRAFT => 1,
            ProjectStatus::PROPOSAL_SUBMITTED => 2,
            ProjectStatus::UNDER_SUPERVISION => 3,
            ProjectStatus::UNDER_REVIEW => 4,
            ProjectStatus::COMPLETED => 5,
            ProjectStatus::REJECTED => 3, // Pauses at Under Supervision for revisions
        };
    }

    /**
     * Mark every unread supervisor update as read.
     */
    public function markNotificationsRead(): void
    {
        Auth::user()?->unreadNotifications->markAsRead();
    }

    public function render(): View
    {
        return view('livewire.student.student-dashboard', [
            'notifications' => Auth::user()?->notifications()->latest()->take(5)->get() ?? collect(),
            'stepIndex' => $this->getPipelineStepIndex(),
            'evaluations' => $this->project ? $this->project->evaluations : collect(),
            'manuscripts' => $this->project ? $this->project->manuscripts : collect(),
            'isCompleted' => $this->project?->status === ProjectStatus::COMPLETED,
        ]);
    }
}
