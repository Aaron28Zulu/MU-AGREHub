<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Enums\ProjectStatus;
use App\Enums\ResearchCategory;
use App\Models\Project;
use App\Models\SupervisorEvaluation;
use App\Models\User;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class ProjectRepository implements ProjectRepositoryInterface
{
    /**
     * Columns that are strictly safe for public catalog discovery.
     * Note: 'full_document_path' and 'raw_data_path' are deliberately excluded.
     *
     * @var list<string>
     */
    protected const PUBLIC_SAFE_COLUMNS = [
        'id',
        'title',
        'abstract',
        'category',
        'status',
        'completion_year',
        'student_id',
        'supervisor_id',
        'created_at',
        'updated_at',
    ];

    /**
     * Returns paginated projects, strictly excluding restricted manuscripts and datasets.
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
    public function getPublicCatalog(array $filters = []): LengthAwarePaginator
    {
        $perPage = (int) ($filters['per_page'] ?? 12);

        return Project::query()
            ->select(self::PUBLIC_SAFE_COLUMNS)
            ->publicCatalog()
            ->with([
                'student:id,name,department',
                'supervisor:id,name,department',
                'researchGaps:id,project_id,extracted_gap_text,is_emerging',
            ])
            ->when(! empty($filters['search']), function (Builder $query) use ($filters) {
                $term = '%'.trim((string) $filters['search']).'%';
                $like = DB::connection()->getDriverName() === 'pgsql' ? 'ilike' : 'like';
                $field = (string) ($filters['field'] ?? 'all');
                $person = fn (string $relation) => fn (Builder $q) => $q->whereHas(
                    $relation,
                    fn (Builder $userQuery) => $userQuery->where('name', $like, $term)
                );

                match ($field) {
                    'title' => $query->where('title', $like, $term),
                    'abstract' => $query->where('abstract', $like, $term),
                    'author' => $person('student')($query),
                    'supervisor' => $person('supervisor')($query),
                    default => $query->where(function (Builder $subQuery) use ($term, $like) {
                        $subQuery->where('title', $like, $term)
                            ->orWhere('abstract', $like, $term)
                            ->orWhereHas('student', fn (Builder $q) => $q->where('name', $like, $term))
                            ->orWhereHas('supervisor', fn (Builder $q) => $q->where('name', $like, $term))
                            ->orWhereHas('researchGaps', fn (Builder $q) => $q->where('extracted_gap_text', $like, $term));
                    }),
                };
            })
            ->when(! empty($filters['categories'] ?? $filters['category'] ?? null), function (Builder $query) use ($filters) {
                $cats = $filters['categories'] ?? [$filters['category']];
                $cats = is_array($cats) ? array_filter($cats) : [$cats];
                if (! empty($cats)) {
                    $query->whereIn('category', $cats);
                }
            })
            ->when(! empty($filters['years'] ?? $filters['year'] ?? null), function (Builder $query) use ($filters) {
                $years = $filters['years'] ?? [$filters['year']];
                $years = is_array($years) ? array_filter($years) : [$years];
                if (! empty($years)) {
                    $query->whereIn('completion_year', array_map('intval', $years));
                }
            })
            ->when(! empty($filters['only_with_gaps']), function (Builder $query) {
                $query->has('researchGaps');
            })
            ->when(($filters['sort'] ?? 'latest') === 'oldest', function (Builder $query) {
                $query->oldest('completion_year')->oldest('id');
            })
            ->when(($filters['sort'] ?? 'latest') === 'title_asc', function (Builder $query) {
                $query->orderBy('title', 'asc');
            })
            ->when(($filters['sort'] ?? 'latest') === 'year_desc' || ($filters['sort'] ?? 'latest') === 'latest', function (Builder $query) {
                $query->latest('completion_year')->latest('id');
            })
            ->paginate($perPage);
    }

    /**
     * Handles the creation of a new project in the draft or proposal_submitted state.
     *
     * @param  array<string, mixed>  $data
     */
    public function submitProposal(User $student, array $data): Project
    {
        return DB::transaction(function () use ($student, $data): Project {
            $status = isset($data['is_draft']) && $data['is_draft']
                ? ProjectStatus::DRAFT
                : ProjectStatus::PROPOSAL_SUBMITTED;

            $fullDocumentPath = null;
            if (isset($data['full_document']) && $data['full_document'] instanceof UploadedFile) {
                // Stored on private/secure disk
                $fullDocumentPath = $data['full_document']->store(
                    'manuscripts/'.date('Y'),
                    ['disk' => 'secure_research']
                );
            } elseif (isset($data['full_document_path']) && is_string($data['full_document_path'])) {
                $fullDocumentPath = $data['full_document_path'];
            }

            $rawDataPath = null;
            if (isset($data['raw_data']) && $data['raw_data'] instanceof UploadedFile) {
                // Stored on private/secure disk
                $rawDataPath = $data['raw_data']->store(
                    'raw_datasets/'.date('Y'),
                    ['disk' => 'secure_research']
                );
            } elseif (isset($data['raw_data_path']) && is_string($data['raw_data_path'])) {
                $rawDataPath = $data['raw_data_path'];
            }

            $category = $data['category'] instanceof ResearchCategory
                ? $data['category']->value
                : (string) $data['category'];

            /** @var Project $project */
            $project = Project::create([
                'title' => (string) $data['title'],
                'abstract' => (string) $data['abstract'],
                'category' => $category,
                'status' => $status->value,
                'completion_year' => isset($data['completion_year']) ? (int) $data['completion_year'] : null,
                'supervisor_id' => isset($data['supervisor_id']) ? (int) $data['supervisor_id'] : null,
                'student_id' => $student->id,
                'full_document_path' => $fullDocumentPath,
                'raw_data_path' => $rawDataPath,
            ]);

            // Track initial manuscript version
            if ($fullDocumentPath) {
                $fileName = isset($data['full_document']) && $data['full_document'] instanceof UploadedFile
                    ? $data['full_document']->getClientOriginalName()
                    : basename($fullDocumentPath);

                $fileSize = isset($data['full_document']) && $data['full_document'] instanceof UploadedFile
                    ? $data['full_document']->getSize()
                    : null;

                $project->manuscripts()->create([
                    'version' => 'v1.0',
                    'milestone_stage' => 'Proposal',
                    'file_path' => $fullDocumentPath,
                    'file_name' => $fileName,
                    'file_size' => $fileSize,
                    'submission_notes' => 'Initial dissertation research proposal draft.',
                    'status' => $status === ProjectStatus::DRAFT ? 'draft' : 'submitted',
                ]);
            }

            // If research gaps were supplied during submission
            if (! empty($data['research_gaps']) && is_array($data['research_gaps'])) {
                foreach ($data['research_gaps'] as $gap) {
                    $text = is_array($gap) ? (string) ($gap['extracted_gap_text'] ?? $gap['description'] ?? '') : (string) $gap;
                    if (! empty(trim($text))) {
                        $project->researchGaps()->create([
                            'extracted_gap_text' => trim($text),
                            'is_emerging' => is_array($gap) ? (bool) ($gap['is_emerging'] ?? false) : false,
                        ]);
                    }
                }
            }

            return $project;
        });
    }

    /**
     * Saves the supervisor evaluation and transitions the project state.
     *
     * @param array{
     *     rubric_score: float|int,
     *     feedback?: ?string,
     *     document_markup_path?: ?string,
     *     new_status?: ?string
     * } $evaluationData
     */
    public function gradeProject(Project $project, User $supervisor, array $evaluationData): SupervisorEvaluation
    {
        return DB::transaction(function () use ($project, $supervisor, $evaluationData): SupervisorEvaluation {
            $rubricScore = (float) $evaluationData['rubric_score'];
            $feedback = isset($evaluationData['feedback']) ? (string) $evaluationData['feedback'] : null;
            $markupPath = isset($evaluationData['document_markup_path']) ? (string) $evaluationData['document_markup_path'] : null;

            /** @var SupervisorEvaluation $evaluation */
            $evaluation = SupervisorEvaluation::create([
                'project_id' => $project->id,
                'supervisor_id' => $supervisor->id,
                'rubric_score' => $rubricScore,
                'feedback' => $feedback,
                'document_markup_path' => $markupPath,
            ]);

            // Transition project status
            if (! empty($evaluationData['new_status'])) {
                $targetStatus = ProjectStatus::from((string) $evaluationData['new_status']);
            } else {
                // Automated transition rule: >= 50 marks as completed, otherwise under_review/rejected
                $targetStatus = $rubricScore >= 50.0
                    ? ProjectStatus::COMPLETED
                    : ProjectStatus::UNDER_REVIEW;
            }

            $updatePayload = [
                'status' => $targetStatus->value,
            ];

            if ($targetStatus === ProjectStatus::COMPLETED && $project->completion_year === null) {
                $updatePayload['completion_year'] = (int) date('Y');
            }

            $project->update($updatePayload);

            return $evaluation;
        });
    }
}
