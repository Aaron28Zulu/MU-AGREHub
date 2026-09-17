<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ProjectStatus;
use App\Enums\ResearchCategory;
use App\Enums\SimilarityCheckStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\URL;

class Project extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'abstract',
        'category',
        'status',
        'similarity_check_status',
        'completion_year',
        'supervisor_id',
        'student_id',
        'full_document_path',
        'raw_data_path',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ProjectStatus::class,
            'similarity_check_status' => SimilarityCheckStatus::class,
            'category' => ResearchCategory::class,
            'completion_year' => 'integer',
        ];
    }

    /**
     * Primary student author of the project.
     *
     * @return BelongsTo<User, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Assigned academic supervisor.
     *
     * @return BelongsTo<User, $this>
     */
    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    /**
     * Research gaps identified and linked to this project.
     *
     * @return HasMany<ResearchGap, $this>
     */
    public function researchGaps(): HasMany
    {
        return $this->hasMany(ResearchGap::class);
    }

    /**
     * Supervisor reviews and evaluations for this project.
     *
     * @return HasMany<SupervisorEvaluation, $this>
     */
    public function evaluations(): HasMany
    {
        return $this->hasMany(SupervisorEvaluation::class);
    }

    /**
     * Versioned manuscripts submitted for this project.
     *
     * @return HasMany<ProjectManuscript, $this>
     */
    public function manuscripts(): HasMany
    {
        return $this->hasMany(ProjectManuscript::class)->latest();
    }

    /**
     * The most recently uploaded manuscript version.
     *
     * @return HasOne<ProjectManuscript, $this>
     */
    public function latestManuscript(): HasOne
    {
        return $this->hasOne(ProjectManuscript::class)->latestOfMany();
    }

    /**
     * The most recent supervisor evaluation.
     *
     * @return HasOne<SupervisorEvaluation, $this>
     */
    public function latestEvaluation(): HasOne
    {
        return $this->hasOne(SupervisorEvaluation::class)->latestOfMany();
    }

    /**
     * Plagiarism & similarity reports generated for this project.
     *
     * @return HasMany<SimilarityReport, $this>
     */
    public function similarityReports(): HasMany
    {
        return $this->hasMany(SimilarityReport::class);
    }

    /**
     * The most recent similarity report.
     *
     * @return HasOne<SimilarityReport, $this>
     */
    public function latestSimilarityReport(): HasOne
    {
        return $this->hasOne(SimilarityReport::class)->latestOfMany();
    }

    /**
     * The similarity report linked to this project.
     *
     * @return HasOne<SimilarityReport, $this>
     */
    public function similarityReport(): HasOne
    {
        return $this->hasOne(SimilarityReport::class)->latestOfMany();
    }

    /**
     * Scope a query to only include completed projects visible to public discovery.
     *
     * @param  Builder<Project>  $query
     */
    public function scopePublicCatalog(Builder $query): void
    {
        $query->where('status', ProjectStatus::COMPLETED->value);
    }

    /**
     * Generate a secure, temporary signed URL pointing to the manuscript file for in-browser preview.
     */
    public function getDocumentUrl(): string
    {
        if (! $this->full_document_path) {
            return '';
        }

        return URL::temporarySignedRoute(
            'projects.document.preview',
            now()->addMinutes(30),
            ['project' => $this->id]
        );
    }
}
