<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ResearchCategory;
use App\Enums\SiteStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A field, lab, school, clinic or community site where students collect data
 * that builds a shared dataset for research.
 */
class ResearchSite extends Model
{
    /**
     * Mirrors the column defaults so freshly created models behave like stored ones.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'setup',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'focus',
        'category',
        'method',
        'location',
        'period',
        'status',
        'started_on',
        'ends_on',
        'objectives',
        'coordinator_id',
        'project_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => SiteStatus::class,
            'category' => ResearchCategory::class,
            'started_on' => 'date',
            'ends_on' => 'date',
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
     * Research project the site's data feeds into, if any.
     *
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * @return HasMany<SiteObservation, $this>
     */
    public function observations(): HasMany
    {
        return $this->hasMany(SiteObservation::class);
    }

    /**
     * Days since data collection started, or null before it begins.
     */
    public function daysActive(): ?int
    {
        return $this->started_on && $this->started_on->isPast()
            ? (int) $this->started_on->diffInDays(now())
            : null;
    }
}
