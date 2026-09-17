<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\ResearchCategory;
use App\Models\User;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use Illuminate\Contracts\View\View;
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

        $repository->submitProposal($student, [
            'title' => $this->title,
            'abstract' => $fullAbstract,
            'category' => $this->category,
            'supervisor_id' => $this->supervisor_id,
            'full_document' => $this->full_document,
            'research_gaps' => array_filter(array_map('trim', $this->research_gaps)),
            'is_draft' => $this->is_draft,
        ]);

        session()->flash('success', $this->is_draft ? 'Proposal saved as draft.' : 'Dissertation research proposal registered successfully.');
        $this->redirectRoute('student.dashboard');
    }

    public function render(): View
    {
        $supervisors = User::role('supervisor')->get(['id', 'name', 'department']);

        return view('livewire.student-submission-wizard', [
            'categories' => ResearchCategory::cases(),
            'supervisors' => $supervisors,
        ]);
    }
}
