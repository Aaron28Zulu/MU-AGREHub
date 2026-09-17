<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Project;
use App\Services\ResearchGapAnalysisService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class AnalyzeProjectGapsJob implements ShouldQueue
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
    public array $backoff = [30, 60, 180];

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
    public function handle(ResearchGapAnalysisService $service): void
    {
        Log::info("Starting AI Research Gap extraction for Project #{$this->project->id}: '{$this->project->title}'");

        try {
            $gaps = $service->analyzeCompletedProject($this->project);

            Log::info(sprintf(
                'Successfully extracted and indexed %d research gaps for Project #%d.',
                count($gaps),
                $this->project->id
            ));
        } catch (Exception $e) {
            Log::error("Failed to analyze research gaps for Project #{$this->project->id}: {$e->getMessage()}", [
                'project_id' => $this->project->id,
                'attempt' => $this->attempts(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-throw to trigger backoff retry unless max tries reached
            throw $e;
        }
    }

    /**
     * Handle a job failure after exhausting all attempts.
     */
    public function failed(?Throwable $exception): void
    {
        Log::critical("AnalyzeProjectGapsJob definitively failed for Project #{$this->project->id}: ".($exception?->getMessage() ?? 'Unknown error'));
    }
}
