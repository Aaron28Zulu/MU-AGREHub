<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Project;
use App\Models\SupervisorEvaluation;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProjectRepositoryInterface
{
    /**
     * Retrieve paginated projects for public catalog discovery.
     * Strictly excludes full_document_path and raw_data_path.
     *
     * @param array{
     *     search?: ?string,
     *     field?: ?string,
     *     category?: ?string,
     *     categories?: ?array,
     *     year?: ?int,
     *     years?: ?array,
     *     only_with_gaps?: ?bool,
     *     sort?: ?string,
     *     per_page?: ?int
     * } $filters
     * @return LengthAwarePaginator<Project>
     */
    public function getPublicCatalog(array $filters): LengthAwarePaginator;

    /**
     * Submit or draft a research project proposal on behalf of a student.
     *
     * @param  array<string, mixed>  $data
     */
    public function submitProposal(User $student, array $data): Project;

    /**
     * Record a supervisor rubric grading and transition project status.
     *
     * @param array{
     *     rubric_score: float|int,
     *     feedback?: ?string,
     *     document_markup_path?: ?string,
     *     new_status?: ?string
     * } $evaluationData
     */
    public function gradeProject(Project $project, User $supervisor, array $evaluationData): SupervisorEvaluation;
}
