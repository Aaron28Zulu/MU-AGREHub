<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ResearchDomain extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Extracted gaps linked to this thematic research domain.
     *
     * @return HasMany<ResearchGap, $this>
     */
    public function researchGaps(): HasMany
    {
        return $this->hasMany(ResearchGap::class, 'domain_id');
    }
}
