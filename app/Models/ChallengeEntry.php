<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\EntryStatus;
use App\Enums\EntryType;
use App\Enums\ResearchCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A student's research proposal or innovative project entered into an innovation challenge.
 */
class ChallengeEntry extends Model
{
    /**
     * Mirrors the column defaults so freshly created models behave like stored ones.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'submitted',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'innovation_challenge_id',
        'student_id',
        'title',
        'entry_type',
        'category',
        'problem_statement',
        'proposed_solution',
        'expected_impact',
        'team_members',
        'attachment_path',
        'status',
        'award',
        'office_feedback',
        'mentor_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'entry_type' => EntryType::class,
            'category' => ResearchCategory::class,
            'status' => EntryStatus::class,
        ];
    }

    /**
     * @return BelongsTo<InnovationChallenge, $this>
     */
    public function challenge(): BelongsTo
    {
        return $this->belongsTo(InnovationChallenge::class, 'innovation_challenge_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Lecturer assigned by the office to guide a selected project.
     *
     * @return BelongsTo<User, $this>
     */
    public function mentor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    /**
     * Scores recorded by members of the review panel.
     *
     * @return HasMany<EntryReview, $this>
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(EntryReview::class);
    }

    /**
     * Entries the office has shortlisted or recognised, which may be shown publicly.
     *
     * @param  Builder<ChallengeEntry>  $query
     */
    public function scopePubliclyListed(Builder $query): void
    {
        $query->whereIn('status', [EntryStatus::SHORTLISTED->value, EntryStatus::RECOGNISED->value]);
    }

    /**
     * Mean panel score out of 20, or null before anyone has scored the entry.
     */
    public function panelScore(): ?float
    {
        $reviews = $this->relationLoaded('reviews') ? $this->reviews : $this->reviews()->get();

        if ($reviews->isEmpty()) {
            return null;
        }

        return round($reviews->avg(fn (EntryReview $review): int => $review->total()), 1);
    }
}
