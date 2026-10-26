<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PlotStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A field plot in the Continuous Crop Production Initiative, where students practise
 * production and collect field data for research.
 */
class CropPlot extends Model
{
    /**
     * Mirrors the column defaults so freshly created models behave like stored ones.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => 'preparing',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'crop',
        'variety',
        'location',
        'area_m2',
        'season',
        'status',
        'planted_on',
        'expected_harvest_on',
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
            'status' => PlotStatus::class,
            'area_m2' => 'decimal:2',
            'planted_on' => 'date',
            'expected_harvest_on' => 'date',
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
     * Research project the plot's data feeds into, if any.
     *
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * @return HasMany<PlotObservation, $this>
     */
    public function observations(): HasMany
    {
        return $this->hasMany(PlotObservation::class);
    }

    /**
     * Days since planting, or null before the crop is in the ground.
     */
    public function daysAfterPlanting(): ?int
    {
        return $this->planted_on && $this->planted_on->isPast()
            ? (int) $this->planted_on->diffInDays(now())
            : null;
    }
}
