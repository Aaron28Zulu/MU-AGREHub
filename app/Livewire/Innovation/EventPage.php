<?php

declare(strict_types=1);

namespace App\Livewire\Innovation;

use App\Enums\EventType;
use App\Models\AssociationEvent;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

/**
 * An office event (debate, workshop, seminar or field visit) with sign-up and recorded outcomes.
 */
#[Layout('layouts.app')]
class EventPage extends Component
{
    /**
     * Sides a debater can argue.
     *
     * @var array<string, string>
     */
    public const POSITIONS = [
        'proposition' => 'Proposition (for the motion)',
        'opposition' => 'Opposition (against the motion)',
    ];

    public AssociationEvent $record;

    public bool $wants_to_speak = false;

    public string $position = '';

    public function mount(AssociationEvent $event): void
    {
        abort_unless($event->is_published, 404);

        $this->record = $event;
    }

    public function signUp(): void
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user) {
            $this->redirectRoute('login');

            return;
        }

        if ($this->record->hasStarted()) {
            $this->addError('registration', 'Registration has closed for this event.');

            return;
        }

        $isDebate = $this->record->event_type === EventType::DEBATE;

        $this->validate([
            'wants_to_speak' => ['boolean'],
            'position' => [Rule::requiredIf($isDebate && $this->wants_to_speak), 'nullable', Rule::in(array_keys(self::POSITIONS))],
        ], [
            'position.required' => 'Choose the side you would like to argue.',
        ]);

        if ($user->registeredEvents()->whereKey($this->record->id)->exists()) {
            return;
        }

        if (! $this->record->hasSeatsLeft()) {
            $this->addError('registration', 'This event is full.');

            return;
        }

        if ($this->wants_to_speak && ! $this->record->hasSpeakerSlotsLeft()) {
            $this->addError('registration', 'All speaker slots are taken, but you can still register to attend.');

            return;
        }

        $user->registeredEvents()->attach($this->record->id, [
            'wants_to_speak' => $this->wants_to_speak,
            'position' => $isDebate && $this->wants_to_speak ? $this->position : null,
        ]);

        session()->flash('success', $this->wants_to_speak ? 'You are registered and on the speakers list.' : 'You are registered for this event.');
        $this->redirectRoute('innovation.event', $this->record);
    }

    public function cancel(): void
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $this->record->hasStarted()) {
            $user->registeredEvents()->detach($this->record->id);
        }

        $this->reset(['wants_to_speak', 'position']);
    }

    public function render(): View
    {
        $registration = Auth::check()
            ? $this->record->attendees()->whereKey(Auth::id())->first()?->pivot
            : null;

        $speakers = $this->record->attendees()->wherePivot('wants_to_speak', true)->get(['users.id', 'users.name']);

        return view('livewire.innovation.event-page', [
            'registration' => $registration,
            'attendeeCount' => $this->record->attendees()->count(),
            'speakers' => $speakers,
            'isDebate' => $this->record->event_type === EventType::DEBATE,
            'positions' => self::POSITIONS,
        ])->title($this->record->title);
    }
}
