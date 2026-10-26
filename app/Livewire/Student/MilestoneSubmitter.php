<?php

declare(strict_types=1);

namespace App\Livewire\Student;

use App\Enums\ProjectStatus;
use App\Enums\SimilarityCheckStatus;
use App\Jobs\RunSimilarityAnalysisJob;
use App\Models\Project;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class MilestoneSubmitter extends Component
{
    use WithFileUploads;

    public Project $project;

    #[Validate('required|file|mimes:pdf,docx|max:10240', message: [
        'required' => 'Please select a research manuscript or draft to upload.',
        'mimes' => 'Only PDF (.pdf) and Microsoft Word (.docx) documents are accepted.',
        'max' => 'Document size must not exceed 10 megabytes.',
    ])]
    public $document = null;

    #[Validate('nullable|string|max:1000')]
    public string $submission_notes = '';

    public function mount(Project $project): void
    {
        // Enforce strict project ownership guard
        if ($project->student_id !== Auth::id()) {
            abort(403, 'Unauthorized action on research project.');
        }

        $this->project = $project;
    }

    public function getIsUnderReviewProperty(): bool
    {
        return $this->project->status === ProjectStatus::UNDER_REVIEW;
    }

    public function getIsCompletedProperty(): bool
    {
        return $this->project->status === ProjectStatus::COMPLETED;
    }

    public function getHasRevisionsRequestedProperty(): bool
    {
        return ($this->project->status === ProjectStatus::UNDER_SUPERVISION || $this->project->status === ProjectStatus::REJECTED)
            && $this->project->evaluations()->exists();
    }

    /**
     * Submit milestone document draft and transition project state.
     */
    public function submit(): void
    {
        // If locked under review or completed, prevent submission
        if ($this->isUnderReview || $this->isCompleted) {
            return;
        }

        $this->validate();

        // Prevent unauthorized tampering
        if ($this->project->student_id !== Auth::id()) {
            abort(403, 'Unauthorized action on research project.');
        }

        // 1. Secure file storage on the private 'secure_research' disk
        $extension = $this->document->getClientOriginalExtension();
        $originalFilename = $this->document->getClientOriginalName();
        $fileSize = $this->document->getSize();

        $uniqueFilename = sprintf(
            'manuscripts/%d/%s_%s.%s',
            $this->project->id,
            now()->format('Ymd_His'),
            str($this->project->title)->slug()->limit(30),
            $extension
        );

        $storedPath = Storage::disk('secure_research')->putFileAs(
            dirname($uniqueFilename),
            $this->document,
            basename($uniqueFilename)
        );

        // 2. Determine version tag and milestone stage
        $existingCount = $this->project->manuscripts()->count();
        $version = sprintf('v%d.%d', intdiv($existingCount, 2) + 1, ($existingCount % 2) + 1);
        if ($existingCount === 0) {
            $version = 'v1.0';
        }

        $milestoneStage = match ($this->project->status) {
            ProjectStatus::DRAFT => 'Proposal',
            ProjectStatus::PROPOSAL_SUBMITTED => 'Revised Proposal',
            ProjectStatus::UNDER_SUPERVISION => ($existingCount >= 2 ? 'Final Draft' : 'Progress Report'),
            ProjectStatus::REJECTED => 'Revision Resubmission',
            default => 'Manuscript Draft',
        };

        // 3. Create manuscript iteration record
        $this->project->manuscripts()->create([
            'version' => $version,
            'milestone_stage' => $milestoneStage,
            'file_path' => $storedPath,
            'file_name' => $originalFilename,
            'file_size' => $fileSize,
            'submission_notes' => $this->submission_notes ?: null,
            'status' => 'under_review',
        ]);

        // 4. Determine state transition based on current milestone stage
        $currentStatus = $this->project->status;
        $nextStatus = match ($currentStatus) {
            ProjectStatus::DRAFT => ProjectStatus::PROPOSAL_SUBMITTED,
            ProjectStatus::UNDER_SUPERVISION, ProjectStatus::REJECTED => ProjectStatus::UNDER_REVIEW,
            default => ProjectStatus::UNDER_REVIEW,
        };

        // 5. Update project record
        $this->project->update([
            'full_document_path' => $storedPath,
            'status' => $nextStatus->value,
        ]);

        // 6. Queue the originality check and alert the supervisor
        $this->project->update(['similarity_check_status' => SimilarityCheckStatus::PROCESSING]);
        RunSimilarityAnalysisJob::dispatch($this->project);

        if ($this->project->supervisor) {
            Notification::make()
                ->title('New submission awaiting review')
                ->body("{$this->project->student?->name} uploaded {$version} ({$milestoneStage}) for '{$this->project->title}'.")
                ->icon('heroicon-o-document-arrow-up')
                ->sendToDatabase($this->project->supervisor);
        }

        // 7. Reset component form state
        $this->reset(['document', 'submission_notes']);

        // 8. Notify user and emit reactive event for parent dashboard
        session()->flash('submission_success', "Milestone draft ({$version} · {$milestoneStage}) uploaded successfully. Status transitioned to {$nextStatus->label()}.");
        $this->dispatch('milestone-submitted');
    }

    public function render(): View
    {
        return view('livewire.student.milestone-submitter', [
            'isUnderReview' => $this->isUnderReview,
            'isCompleted' => $this->isCompleted,
            'hasRevisionsRequested' => $this->hasRevisionsRequested,
            'latestEvaluation' => $this->project->latestEvaluation,
            'supervisor' => $this->project->supervisor,
        ]);
    }
}
