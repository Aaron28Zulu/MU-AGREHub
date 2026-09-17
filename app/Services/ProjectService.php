<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Project;
use App\Models\SupervisorEvaluation;
use App\Models\User;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Gate;

class ProjectService
{
    public function __construct(
        protected readonly ProjectRepositoryInterface $repository
    ) {}

    /**
     * Retrieve public catalog entries without manuscript paths.
     *
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<Project>
     */
    public function getPublicCatalog(array $filters = []): LengthAwarePaginator
    {
        return $this->repository->getPublicCatalog($filters);
    }

    /**
     * Submit proposal after validating student policy permissions.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws AuthorizationException
     */
    public function submitProposal(User $student, array $data): Project
    {
        if (Gate::forUser($student)->denies('create', Project::class)) {
            throw new AuthorizationException('User does not have permission to submit a research project proposal.');
        }

        return $this->repository->submitProposal($student, $data);
    }

    /**
     * Grade and evaluate project after verifying supervisor authorization.
     *
     * @param array{
     *     rubric_score: float|int,
     *     feedback?: ?string,
     *     document_markup_path?: ?string,
     *     new_status?: ?string
     * } $evaluationData
     *
     * @throws AuthorizationException
     */
    public function gradeProject(Project $project, User $supervisor, array $evaluationData): SupervisorEvaluation
    {
        if (Gate::forUser($supervisor)->denies('evaluate', $project)) {
            throw new AuthorizationException('You are not authorized to evaluate or grade this project.');
        }

        return $this->repository->gradeProject($project, $supervisor, $evaluationData);
    }
}
