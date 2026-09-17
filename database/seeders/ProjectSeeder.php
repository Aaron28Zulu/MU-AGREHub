<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ProjectStatus;
use App\Enums\ResearchCategory;
use App\Models\Project;
use App\Models\ResearchDomain;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $supervisor = User::where('email', 'cmulenga@agrihub.edu')->first() ?? User::first();
        $student = User::where('email', 'kelvin@agrihub.edu')->first() ?? User::first();
        $researcher = User::where('email', 'lukundo@agrihub.edu')->first() ?? $student;

        $projects = [
            [
                'title' => 'Assessing Biochar Amendments on Soil Microbial Biomass and Nitrogen Dynamics in Semi-Arid Sandy Soils',
                'abstract' => 'This study investigates the multi-season efficacy of locally pyrolyzed maize stover biochar in enhancing moisture retention, cation exchange capacity, and microbial respiration across smallholder agricultural soils in Southern Zambia. Results demonstrated a 34% increase in plant-available water and significant mitigation of nitrate leaching.',
                'category' => ResearchCategory::CROP_SCIENCE,
                'status' => ProjectStatus::COMPLETED,
                'completion_year' => 2025,
                'student_id' => $student->id,
                'supervisor_id' => $supervisor->id,
                'domain' => 'Soil Fertility & Nutrient Management',
                'gaps' => [
                    'Long-term field trials required to examine biochar aging effects on soil macro-aggregate stability.',
                    'Quantifying economics of farm-scale retort kilns for smallholder farmer cooperatives.',
                ],
            ],
            [
                'title' => 'Formulation of Low-Cost Black Soldier Fly Larvae Meal as a Replacement for Fishmeal in Broiler Diets',
                'abstract' => 'To combat escalating poultry feed expenses, this research formulated five isonitrogenous diets substituting traditional fishmeal with Hermetia illucens larval protein (0%, 25%, 50%, 75%, 100%). Growth performance, feed conversion ratios (FCR), and carcass characteristics were evaluated over a 42-day trial.',
                'category' => ResearchCategory::ANIMAL_SCIENCE,
                'status' => ProjectStatus::COMPLETED,
                'completion_year' => 2024,
                'student_id' => $researcher->id,
                'supervisor_id' => $supervisor->id,
                'domain' => 'Animal Nutrition & Livestock Genetics',
                'gaps' => [
                    'Substrate optimization using localized brewery spent grains to enhance larval lipid profiles.',
                    'Sensory evaluation of broiler meat from full insect-meal diets among urban consumer demographics.',
                ],
            ],
            [
                'title' => 'Digital Aggregation Platforms and Market Access for Rural Smallholder Tomato Growers',
                'abstract' => 'Examining post-harvest losses and price volatility among peri-urban horticultural producers. The study analyzed the adoption of mobile marketplace cooperatives in reducing intermediary markup and improving farm-gate gross margins.',
                'category' => ResearchCategory::AGRIBUSINESS,
                'status' => ProjectStatus::COMPLETED,
                'completion_year' => 2024,
                'student_id' => $student->id,
                'supervisor_id' => $supervisor->id,
                'domain' => 'Agribusiness & Post-Harvest Value Chains',
                'gaps' => [
                    'Cold-chain logistics integration with decentralized solar-powered micro-chillers.',
                ],
            ],
            [
                'title' => 'Biological Control of Fall Armyworm (Spodoptera frugiperda) Utilizing Endophytic Beauveria bassiana Strains',
                'abstract' => 'Evaluating entomopathogenic fungal inoculation in Zea mays to suppress larval herbivory and foliar damage. Laboratory bioassays and micro-plot field trials assessed endophytic colonisation rates, larval mortality, and non-target predator safety.',
                'category' => ResearchCategory::PLANT_PROTECTION,
                'status' => ProjectStatus::COMPLETED,
                'completion_year' => 2025,
                'student_id' => $student->id,
                'supervisor_id' => $supervisor->id,
                'domain' => 'Plant Protection & Integrated Pest Management',
                'gaps' => [
                    'Evaluating UV-stable bioformulations for semi-arid agro-ecological zones with high insolation.',
                    'Compatibility trials with synthetic botanical oil adjuvants.',
                ],
            ],
        ];

        foreach ($projects as $item) {
            /** @var Project $project */
            $project = Project::updateOrCreate(
                ['title' => $item['title']],
                [
                    'abstract' => $item['abstract'],
                    'category' => $item['category'],
                    'status' => $item['status'],
                    'completion_year' => $item['completion_year'],
                    'student_id' => $item['student_id'],
                    'supervisor_id' => $item['supervisor_id'],
                ]
            );

            // Link domain and gaps
            $domain = ResearchDomain::firstOrCreate(
                ['slug' => Str::slug($item['domain'])],
                ['name' => $item['domain']]
            );

            foreach ($item['gaps'] as $gapText) {
                $project->researchGaps()->firstOrCreate(
                    ['extracted_gap_text' => $gapText],
                    [
                        'domain_id' => $domain->id,
                        'is_emerging' => true,
                        'is_saturated' => false,
                    ]
                );
            }
        }
    }
}
