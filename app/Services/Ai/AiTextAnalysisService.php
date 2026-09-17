<?php

declare(strict_types=1);

namespace App\Services\Ai;

use App\Services\Ai\Contracts\AiTextAnalysisServiceInterface;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiTextAnalysisService implements AiTextAnalysisServiceInterface
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
    public function extractResearchGapsAndDomain(string $text): array
    {
        $apiKey = config('services.llm.api_key') ?? env('OPENAI_API_KEY') ?? env('ANTHROPIC_API_KEY');

        // If an API key is provided, execute HTTP request to LLM API
        if ($apiKey) {
            try {
                $response = Http::withToken($apiKey)
                    ->timeout(30)
                    ->retry(2, 500)
                    ->post('https://api.openai.com/v1/chat/completions', [
                        'model' => 'gpt-4o-mini',
                        'messages' => [
                            [
                                'role' => 'system',
                                'content' => 'You are an agricultural research director. Analyze the project abstract and methodology. Extract 1 to 3 distinct future research gaps or unaddressed questions. Return ONLY a valid JSON array of objects with keys: "domain_name" (e.g., Soil Health, Precision Irrigation, Post-Harvest Physiology), "gap_text" (concise direction), "is_emerging" (boolean).',
                            ],
                            [
                                'role' => 'user',
                                'content' => $text,
                            ],
                        ],
                        'response_format' => ['type' => 'json_object'],
                    ]);

                if ($response->successful()) {
                    $json = $response->json();
                    $content = $json['choices'][0]['message']['content'] ?? '';
                    $decoded = json_decode($content, true);

                    if (isset($decoded['gaps']) && is_array($decoded['gaps'])) {
                        return $decoded['gaps'];
                    }

                    if (is_array($decoded) && array_is_list($decoded)) {
                        return $decoded;
                    }
                }
            } catch (Exception $e) {
                Log::warning('LLM Gap Analysis API error: '.$e->getMessage().'. Falling back to rule-based extraction.');
            }
        }

        // Deterministic heuristic fallback for offline or development environments
        return $this->extractHeuristicGaps($text);
    }

    /**
     * Fallback heuristic extractor when external LLM endpoints are offline or unconfigured.
     *
     * @return list<array{
     *     domain_name: string,
     *     gap_text: string,
     *     is_emerging: bool
     * }>
     */
    protected function extractHeuristicGaps(string $text): array
    {
        $domains = [
            'Soil Fertility & Nutrient Management' => ['soil', 'nitrogen', 'phosphorus', 'fertilizer', 'compost'],
            'Crop Protection & Pest Resistance' => ['pest', 'insects', 'pathogen', 'fungus', 'weeds', 'resistance'],
            'Precision Irrigation & Water Conservation' => ['water', 'irrigation', 'drought', 'moisture', 'hydrology'],
            'Agribusiness & Post-Harvest Value Chains' => ['yield', 'market', 'storage', 'supply chain', 'spoilage'],
            'Animal Nutrition & Livestock Genetics' => ['poultry', 'dairy', 'cattle', 'feed', 'livestock', 'ruminant'],
        ];

        $matchedDomain = 'General Agricultural Sciences';
        $lowerText = strtolower($text);

        foreach ($domains as $domain => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($lowerText, $keyword)) {
                    $matchedDomain = $domain;
                    break 2;
                }
            }
        }

        return [
            [
                'domain_name' => $matchedDomain,
                'gap_text' => 'Longitudinal field trials required across diverse agro-ecological zones to validate initial experimental findings.',
                'is_emerging' => true,
            ],
            [
                'domain_name' => $matchedDomain,
                'gap_text' => 'Socio-economic feasibility and farmer adoption models for smallholder farming communities.',
                'is_emerging' => false,
            ],
        ];
    }
}
