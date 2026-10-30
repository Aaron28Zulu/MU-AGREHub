<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\ProjectStatus;
use App\Enums\ResearchCategory;
use App\Models\Project;
use App\Models\User;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class StudentSubmissionWizard extends Component
{
    use WithFileUploads;

    public string $title = '';

    public string $category = '';

    public string $abstract = ''; // Background & Problem Statement

    public string $objectives_methodology = ''; // Proposed Objectives & Field Methodology

    public ?int $supervisor_id = null;

    /** @var mixed */
    public $full_document = null;

    /** @var list<string> */
    public array $research_gaps = [''];

    public bool $is_draft = false;

    /**
     * Validation rules for the proposal wizard.
     *
     * @return array<string, list<string>>
     */
    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:5', 'max:255'],
            'category' => ['required', 'string'],
            'abstract' => ['required', 'string', 'min:50'],
            'objectives_methodology' => ['nullable', 'string', 'max:5000'],
            'supervisor_id' => ['nullable', 'integer', 'exists:users,id'],
            'full_document' => ['nullable', 'file', 'mimes:pdf,docx', 'max:20480'], // 20MB max
            'research_gaps' => ['nullable', 'array'],
            'research_gaps.*' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Students run one active study at a time; send them back to it instead of registering a duplicate.
     */
    public function mount(): void
    {
        $hasActiveProject = Project::query()
            ->where('student_id', Auth::id())
            ->whereNotIn('status', [ProjectStatus::COMPLETED->value, ProjectStatus::REJECTED->value])
            ->exists();

        if ($hasActiveProject) {
            session()->flash('success', 'You already have an active research project. Continue it from your workspace.');
            $this->redirectRoute('student.dashboard');
        }
    }

    public function addGap(): void
    {
        $this->research_gaps[] = '';
    }

    public function removeGap(int $index): void
    {
        unset($this->research_gaps[$index]);
        $this->research_gaps = array_values($this->research_gaps);
    }

    /**
     * Save the proposal without sending it to a supervisor yet.
     */
    public function saveDraft(ProjectRepositoryInterface $repository): void
    {
        $this->is_draft = true;
        $this->submit($repository);
    }

    /**
     * Completed archive records whose title or abstract share key terms with the proposed title.
     *
     * @return Collection<int, Project>
     */
    protected function similarRecords(): Collection
    {
        return Project::catalogMatchesFor($this->title);
    }

    /**
     * Submit proposal or save as draft via ProjectRepository.
     */
    public function submit(ProjectRepositoryInterface $repository): void
    {
        $this->validate();

        /** @var User $student */
        $student = Auth::user();

        // Concatenate background and methodology if both provided
        $fullAbstract = trim($this->abstract);
        if (! empty(trim($this->objectives_methodology))) {
            $fullAbstract .= "\n\nObjectives & Methodology Outline:\n".trim($this->objectives_methodology);
        }

        $project = $repository->submitProposal($student, [
            'title' => $this->title,
            'abstract' => $fullAbstract,
            'category' => $this->category,
            'supervisor_id' => $this->supervisor_id,
            'full_document' => $this->full_document,
            'research_gaps' => array_filter(array_map('trim', $this->research_gaps)),
            'is_draft' => $this->is_draft,
        ]);

        if (! $this->is_draft && $project->supervisor) {
            Notification::make()
                ->title('New research proposal')
                ->body("{$student->name} submitted '{$project->title}' for your approval.")
                ->icon('heroicon-o-light-bulb')
                ->sendToDatabase($project->supervisor);
        }

        session()->flash('success', $this->is_draft ? 'Proposal saved as draft.' : 'Dissertation research proposal registered successfully.');
        $this->redirectRoute('student.dashboard');
    }

    public function render(): View
    {
        $supervisors = User::role('supervisor')->get(['id', 'name', 'department']);

        return view('livewire.student-submission-wizard', [
            'schools' => ResearchCategory::bySchool(),
            'supervisors' => $supervisors,
            'similarRecords' => $this->similarRecords(),
        ])->title('Register a proposal');
    }
}
