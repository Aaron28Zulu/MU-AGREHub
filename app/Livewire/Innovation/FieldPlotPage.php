<?php

declare(strict_types=1);

namespace App\Livewire\Innovation;

use App\Enums\FieldActivity;
use App\Models\CropPlot;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * A crop production plot: its details, the running field log, and the form students use to add records.
 */
#[Layout('layouts.app')]
class FieldPlotPage extends Component
{
    use WithPagination;

    public CropPlot $plot;

    public string $observed_on = '';

    public string $activity = '';

    public string $metric = '';

    public string $value = '';

    public string $unit = '';

    public string $notes = '';

    public function mount(CropPlot $plot): void
    {
        $this->plot = $plot;
        $this->observed_on = now()->toDateString();
    }

    public function record(): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        abort_unless($user && ($user->isStudent() || $user->managesResearchOffice()), 403);

        if (! $this->plot->status->acceptsObservations()) {
            $this->addError('activity', 'This plot has been harvested and is closed for new records.');

            return;
        }

        $validated = $this->validate([
            'observed_on' => ['required', 'date', 'before_or_equal:today'],
            'activity' => ['required', Rule::enum(FieldActivity::class)],
            'metric' => ['nullable', 'string', 'max:100', 'required_with:value'],
            'value' => ['nullable', 'numeric', 'min:0', 'required_with:metric'],
            'unit' => ['nullable', 'string', 'max:32'],
            'notes' => ['nullable', 'string', 'max:2000', 'required_without:metric'],
        ], [
            'notes.required_without' => 'Add a note or a measurement so the record is useful.',
        ]);

        $this->plot->observations()->create([
            ...array_map(fn ($field) => $field === '' ? null : $field, $validated),
            'recorded_by' => $user->id,
        ]);

        $this->reset(['activity', 'metric', 'value', 'unit', 'notes']);
        $this->resetPage();

        session()->flash('success', 'Field record saved.');
    }

    public function render(): View
    {
        /** @var User|null $user */
        $user = Auth::user();

        return view('livewire.innovation.field-plot-page', [
            'observations' => $this->plot->observations()
                ->with('recorder:id,name')
                ->latest('observed_on')
                ->latest('id')
                ->paginate(15),
            'canRecord' => $user && ($user->isStudent() || $user->managesResearchOffice()) && $this->plot->status->acceptsObservations(),
            'activities' => FieldActivity::cases(),
            'contributors' => $this->plot->observations()->distinct()->count('recorded_by'),
        ])->title($this->plot->name);
    }
}
