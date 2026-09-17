<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResearchGap extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'project_id',
        'domain_id',
        'extracted_gap_text',
        'is_saturated',
        'is_emerging',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_saturated' => 'boolean',
            'is_emerging' => 'boolean',
        ];
    }

    /**
     * The research project from which this gap was extracted.
     *
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * The overarching research domain associated with this gap.
     *
     * @return BelongsTo<ResearchDomain, $this>
     */
    public function domain(): BelongsTo
    {
        return $this->belongsTo(ResearchDomain::class, 'domain_id');
    }

    /**
     * Description alias for extracted_gap_text.
     */
    public function getDescriptionAttribute(): string
    {
        return $this->extracted_gap_text;
    }
}
