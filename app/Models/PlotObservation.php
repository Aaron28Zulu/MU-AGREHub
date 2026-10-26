<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\FieldActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A dated field record (activity, optional measurement and notes) logged by a student on a crop plot.
 */
class PlotObservation extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'crop_plot_id',
        'recorded_by',
        'observed_on',
        'activity',
        'metric',
        'value',
        'unit',
        'notes',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'activity' => FieldActivity::class,
            'observed_on' => 'date',
            'value' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<CropPlot, $this>
     */
    public function plot(): BelongsTo
    {
        return $this->belongsTo(CropPlot::class, 'crop_plot_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
