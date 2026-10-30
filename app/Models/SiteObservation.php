<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SiteActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A dated record (activity, optional measurement and notes) logged by a student at a research site.
 */
class SiteObservation extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'research_site_id',
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
            'activity' => SiteActivity::class,
            'observed_on' => 'date',
            'value' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<ResearchSite, $this>
     */
    public function site(): BelongsTo
    {
        return $this->belongsTo(ResearchSite::class, 'research_site_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
