<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\SimilarityCheckStatus;
use App\Models\Project;
use App\Models\SimilarityReport;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class RunSimilarityAnalysisJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * Exponential backoff in seconds between attempts.
     *
     * @var list<int>
     */
    public array $backoff = [15, 30, 60];

    /**
     * Maximum execution time in seconds before timeout.
     */
    public int $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly Project $project
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Starting AI similarity & plagiarism analysis for Project #{$this->project->id}: '{$this->project->title}'");

        $this->project->update([
            'similarity_check_status' => SimilarityCheckStatus::PROCESSING,
        ]);

        try {
            // Analyze the project's abstract & manuscript against institutional repository
            $targetText = trim((string) ($this->project->abstract ?? ''));
            $matchedSources = [];
            $maxScore = 0.0;

            if ($targetText !== '') {
                // Cross-reference against existing completed or archived projects in the database
                $existingProjects = Project::query()
                    ->where('id', '!=', $this->project->id)
                    ->whereNotNull('abstract')
                    ->where('abstract', '!=', '')
                    ->limit(25)
                    ->get();

                $targetWords = array_unique(array_filter(str_word_count(strtolower($targetText), 1)));

                foreach ($existingProjects as $comparison) {
                    $comparisonText = trim((string) $comparison->abstract);
                    if ($comparisonText === '') {
                        continue;
                    }

                    $compWords = array_unique(array_filter(str_word_count(strtolower($comparisonText), 1)));
                    $commonWords = array_intersect($targetWords, $compWords);

                    if (count($targetWords) > 0) {
                        $overlapRatio = count($commonWords) / count($targetWords);
                        $percentage = round($overlapRatio * 100, 1);

                        // If overlap exceeds 15%, record as an overlapping academic source
                        if ($percentage >= 15.0) {
                            $studentSentences = preg_split('/(?<=[.?!])\s+/', $targetText, -1, PREG_SPLIT_NO_EMPTY) ?: [$targetText];
                            $compSentences = preg_split('/(?<=[.?!])\s+/', $comparisonText, -1, PREG_SPLIT_NO_EMPTY) ?: [$comparisonText];

                            $matchedSources[] = [
                                'source_project_title' => $comparison->title,
                                'student_text' => $studentSentences[0] ?? $targetText,
                                'original_text' => $compSentences[0] ?? $comparisonText,
                                'percentage' => $percentage,
                            ];

                            if ($percentage > $maxScore) {
                                $maxScore = $percentage;
                            }
                        }
                    }
                }
            }

            // Cap the similarity score at 100.0%
            $finalScore = min(100.0, max(0.0, $maxScore));

            SimilarityReport::updateOrCreate(
                ['project_id' => $this->project->id],
                [
                    'similarity_score' => $finalScore,
                    'matched_sources_json' => $matchedSources,
                ]
            );

            $this->project->update([
                'similarity_check_status' => SimilarityCheckStatus::COMPLETED,
            ]);

            Log::info("Similarity analysis completed for Project #{$this->project->id} with score: {$finalScore}%");
        } catch (Exception $e) {
            Log::error("Similarity analysis failed for Project #{$this->project->id}: {$e->getMessage()}", [
                'project_id' => $this->project->id,
                'trace' => $e->getTraceAsString(),
            ]);

            $this->project->update([
                'similarity_check_status' => SimilarityCheckStatus::FAILED,
            ]);

            throw $e;
        }
    }

    /**
     * Handle job failure after all retries.
     */
    public function failed(?Throwable $exception): void
    {
        Log::critical("RunSimilarityAnalysisJob definitively failed for Project #{$this->project->id}: ".($exception?->getMessage() ?? 'Unknown error'));

        $this->project->update([
            'similarity_check_status' => SimilarityCheckStatus::FAILED,
        ]);
    }
}
