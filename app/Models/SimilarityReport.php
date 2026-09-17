<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SimilarityReport extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'project_id',
        'similarity_score',
        'matched_sources_json',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'similarity_score' => 'float',
            'matched_sources_json' => 'array',
        ];
    }

    /**
     * The project evaluated for similarity.
     *
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the matched sources array.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getMatchedSourcesAttribute(): array
    {
        return $this->matched_sources_json ?? [];
    }
}
