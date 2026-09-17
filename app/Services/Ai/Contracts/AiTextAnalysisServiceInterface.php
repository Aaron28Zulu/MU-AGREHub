<?php

declare(strict_types=1);

namespace App\Services\Ai\Contracts;

interface AiTextAnalysisServiceInterface
{
    /**
     * Analyze research text and extract future research directions along with domain clusters.
     *
     * @return list<array{
     *     domain_name: string,
     *     gap_text: string,
     *     is_emerging: bool
     * }>
     */
    public function extractResearchGapsAndDomain(string $text): array;
}
