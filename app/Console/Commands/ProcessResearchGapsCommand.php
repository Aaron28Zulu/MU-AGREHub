<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\ProjectStatus;
use App\Jobs\AnalyzeProjectGapsJob;
use App\Models\Project;
use Illuminate\Console\Command;

class ProcessResearchGapsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agrihub:process-gaps {--limit=100 : Maximum number of projects to dispatch}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch asynchronous AI research gap analysis for completed projects lacking gap records';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $limit = (int) $this->option('limit');

        $this->info('Scanning completed projects requiring research gap extraction...');

        // Fetch completed projects lacking extracted research gaps
        $projects = Project::query()
            ->where('status', ProjectStatus::COMPLETED->value)
            ->whereDoesntHave('researchGaps')
            ->limit($limit)
            ->get();

        if ($projects->isEmpty()) {
            $this->info('No pending completed projects found. All projects have been processed.');

            return self::SUCCESS;
        }

        $this->info("Found {$projects->count()} projects. Dispatching to background queue...");

        $bar = $this->output->createProgressBar($projects->count());
        $bar->start();

        foreach ($projects as $project) {
            AnalyzeProjectGapsJob::dispatch($project);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Successfully dispatched {$projects->count()} background jobs to the queue.");

        return self::SUCCESS;
    }
}
