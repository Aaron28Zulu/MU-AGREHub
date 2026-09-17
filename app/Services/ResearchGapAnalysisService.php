<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Project;
use App\Models\ResearchDomain;
use App\Models\ResearchGap;
use App\Services\Ai\Contracts\AiTextAnalysisServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ResearchGapAnalysisService
{
    /**
     * Saturation threshold: If a domain has more than this number of completed projects,
     * it is marked as saturated.
     */
    protected const SATURATION_THRESHOLD = 5;

    public function __construct(
        protected readonly AiTextAnalysisServiceInterface $aiService
    ) {}

    /**
     * Analyze a completed project, extract research gaps, and classify them into thematic domains.
     *
     * @return list<ResearchGap>
     */
    public function analyzeCompletedProject(Project $project): array
    {
        // 1. Concatenate project title, abstract, and category metadata
        $textPayload = sprintf(
            "Title: %s\nCategory: %s\nAbstract: %s\nCompletion Year: %s",
            $project->title,
            $project->category->label(),
            $project->abstract,
            $project->completion_year ?? date('Y')
        );

        // 2. Call AI service to extract gaps and domains
        $extractedItems = $this->aiService->extractResearchGapsAndDomain($textPayload);

        $savedGaps = [];

        DB::transaction(function () use ($project, $extractedItems, &$savedGaps): void {
            foreach ($extractedItems as $item) {
                $domainName = trim($item['domain_name'] ?? 'General Agricultural Sciences');
                $gapText = trim($item['gap_text'] ?? '');

                if (empty($gapText)) {
                    continue;
                }

                // 3. Find or create the ResearchDomain by slug
                $slug = Str::slug($domainName);
                /** @var ResearchDomain $domain */
                $domain = ResearchDomain::firstOrCreate(
                    ['slug' => $slug],
                    ['name' => $domainName]
                );

                // 4. Calculate saturation: count completed projects associated with this domain
                $domainProjectCount = ResearchGap::query()
                    ->where('domain_id', $domain->id)
                    ->distinct('project_id')
                    ->count('project_id');

                $isSaturated = $domainProjectCount >= self::SATURATION_THRESHOLD;
                $isEmerging = (bool) ($item['is_emerging'] ?? ! $isSaturated);

                // 5. Store record in research_gaps table
                /** @var ResearchGap $gap */
                $gap = ResearchGap::create([
                    'project_id' => $project->id,
                    'domain_id' => $domain->id,
                    'extracted_gap_text' => $gapText,
                    'is_saturated' => $isSaturated,
                    'is_emerging' => $isEmerging,
                ]);

                $savedGaps[] = $gap;
            }
        });

        return $savedGaps;
    }
}
