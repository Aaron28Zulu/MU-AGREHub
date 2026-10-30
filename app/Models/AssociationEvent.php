<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\EventType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * An activity run by the Office of Research and Innovation, such as an inter-school debate
 * or a research methods workshop.
 */
class AssociationEvent extends Model
{
    /**
     * Mirrors the column defaults so freshly created models behave like stored ones.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'is_published' => true,
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'event_type',
        'motion',
        'description',
        'venue',
        'starts_at',
        'capacity',
        'speaker_slots',
        'outcomes',
        'is_published',
        'coordinator_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'event_type' => EventType::class,
            'starts_at' => 'datetime',
            'capacity' => 'integer',
            'speaker_slots' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function coordinator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coordinator_id');
    }

    /**
     * Students and staff who signed up to attend or speak.
     *
     * @return BelongsToMany<User, $this>
     */
    public function attendees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_registrations')
            ->withPivot(['wants_to_speak', 'position'])
            ->withTimestamps();
    }

    /**
     * @param  Builder<AssociationEvent>  $query
     */
    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true);
    }

    /**
     * @param  Builder<AssociationEvent>  $query
     */
    public function scopeUpcoming(Builder $query): void
    {
        $query->where('starts_at', '>=', now()->startOfDay())->orderBy('starts_at');
    }

    public function hasStarted(): bool
    {
        return $this->starts_at->isPast();
    }

    /**
     * Whether another attendee can still register.
     */
    public function hasSeatsLeft(): bool
    {
        return $this->capacity === null || $this->attendees()->count() < $this->capacity;
    }

    /**
     * Whether another speaker (debater or presenter) can still be accepted.
     */
    public function hasSpeakerSlotsLeft(): bool
    {
        return $this->speaker_slots === null
            || $this->attendees()->wherePivot('wants_to_speak', true)->count() < $this->speaker_slots;
    }
}
