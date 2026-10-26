<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ChallengeStatus;
use App\Enums\EntryStatus;
use App\Enums\EntryType;
use App\Enums\EventType;
use App\Enums\FieldActivity;
use App\Enums\PlotStatus;
use App\Enums\ResearchCategory;
use App\Enums\UserRole;
use App\Models\AssociationEvent;
use App\Models\CropPlot;
use App\Models\InnovationChallenge;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeds the Office of Research and Innovation's semester work plan: the ARIS launch workshop,
 * the Agricultural Debate, the Vision 2030 challenge and the continuous crop production plots.
 */
class ResearchOfficeSeeder extends Seeder
{
    public function run(): void
    {
        $ethel = User::updateOrCreate(['email' => 'ethel@agrihub.edu'], [
            'name' => 'Ethel Kunda',
            'password' => Hash::make('password'),
            'role' => UserRole::RESEARCHER,
            'department' => 'Office of Research and Innovation',
            'email_verified_at' => now(),
        ]);
        $ethel->syncRoles([UserRole::RESEARCHER->value, UserRole::RESEARCH_OFFICER->value]);

        // The assistant secretary keeps his student account and gains office duties.
        $peter = User::where('email', 'peter@agrihub.edu')->first();
        $peter?->assignRole(UserRole::RESEARCH_OFFICER->value);

        $lukundo = User::where('email', 'lukundo@agrihub.edu')->first();
        $kelvin = User::where('email', 'kelvin@agrihub.edu')->first();
        $supervisor = User::where('email', 'cmulenga@agrihub.edu')->first();

        AssociationEvent::updateOrCreate(['title' => 'Project ARIS Launch Workshop'], [
            'event_type' => EventType::WORKSHOP,
            'motion' => 'Preserving and sharing student research',
            'description' => "Official launch of Project ARIS (Agricultural Research Interface System), the school's platform for uploading, storing and finding student research and innovations.\n\nThe workshop demonstrates how the platform works, explains how it helps identify research gaps and avoid duplicated topics, and opens a discussion with students and lecturers on preserving the school's research.",
            'venue' => 'School of Agriculture Lecture Theatre 1',
            'starts_at' => now()->addDays(10)->setTime(14, 0),
            'capacity' => 150,
            'speaker_slots' => 4,
            'coordinator_id' => $ethel->id,
        ]);

        $debate = AssociationEvent::updateOrCreate(['title' => 'MUSAGRICA Agricultural Debate'], [
            'event_type' => EventType::DEBATE,
            'motion' => 'Are climate change and agricultural finance the only barriers to agricultural transformation in Zambia?',
            'description' => "An evidence-based debate on Zambia's agricultural sector. Students critically examine the many factors affecting agricultural transformation, from extension services and markets to land tenure and research uptake, and propose practical, research-based solutions.",
            'venue' => 'Main Hall',
            'starts_at' => now()->addDays(24)->setTime(15, 30),
            'capacity' => 200,
            'speaker_slots' => 8,
            'coordinator_id' => $ethel->id,
        ]);

        if ($lukundo && $kelvin) {
            $debate->attendees()->syncWithoutDetaching([
                $lukundo->id => ['wants_to_speak' => true, 'position' => 'proposition'],
                $kelvin->id => ['wants_to_speak' => false, 'position' => null],
            ]);
        }

        $challenge = InnovationChallenge::updateOrCreate(['edition' => 'Semester 1, 2026/27'], [
            'title' => 'MUSAGRICA Vision 2030 Research and Innovation Challenge',
            'theme' => 'Advancing Agriculture Through Research, Innovation, and Knowledge Sharing',
            'description' => "The Office of Research and Innovation invites research proposals and innovative project ideas that support MUSAGRICA's Vision 2030 and contribute to agricultural development in Zambia.\n\nA review panel evaluates every submission. Shortlisted projects present to the panel, outstanding proposals are recognised, and selected projects receive support and guidance from the office and are published in collaboration with MUSAGRICA.",
            'criteria' => 'Open to all students of the School of Agriculture and Natural Resources, individually or in teams of up to four.',
            'status' => ChallengeStatus::OPEN,
            'opens_on' => now()->subWeek()->toDateString(),
            'closes_on' => now()->addWeeks(3)->toDateString(),
            'presentation_on' => now()->addWeeks(5)->toDateString(),
            'coordinator_id' => $ethel->id,
        ]);

        if ($lukundo && $kelvin) {
            $challenge->entries()->updateOrCreate(['student_id' => $lukundo->id, 'title' => 'Low-cost soil moisture sensors for smallholder irrigation scheduling'], [
                'entry_type' => EntryType::INNOVATION,
                'category' => ResearchCategory::CROP_SCIENCE,
                'problem_statement' => 'Smallholder vegetable growers irrigate by guesswork, wasting scarce water in the dry season and losing yield when crops are stressed between irrigations.',
                'proposed_solution' => 'Build and field-test an Arduino-based soil moisture probe costing under K300 that signals when to irrigate, trialled on the school crop production plots.',
                'expected_impact' => 'Reduced water use and more stable vegetable yields for smallholders, with a design students can reproduce for local farmers.',
                'team_members' => 'Peter Mwaba',
                'status' => EntryStatus::SHORTLISTED,
                'mentor_id' => $supervisor?->id,
            ]);

            $challenge->entries()->updateOrCreate(['student_id' => $kelvin->id, 'title' => 'Community goat pass-on schemes and household income in Central Province'], [
                'entry_type' => EntryType::RESEARCH_PROPOSAL,
                'category' => ResearchCategory::ANIMAL_SCIENCE,
                'problem_statement' => 'Goat pass-on programmes are widely promoted, but there is little evidence on whether they raise household income once the initial donor support ends.',
                'proposed_solution' => 'A household survey of participating and non-participating families in two districts, comparing herd growth, sales and income over three years.',
                'expected_impact' => 'Evidence to help NGOs and the ministry design livestock support that lasts beyond the funding period.',
                'status' => EntryStatus::SUBMITTED,
            ]);
        }

        $maize = CropPlot::updateOrCreate(['name' => 'Block A · Plot 1'], [
            'crop' => 'Maize',
            'variety' => 'SC 403',
            'location' => 'School farm, Block A',
            'area_m2' => 400,
            'season' => '2026/27 rain-fed',
            'status' => PlotStatus::GROWING,
            'planted_on' => now()->subDays(35)->toDateString(),
            'expected_harvest_on' => now()->addDays(85)->toDateString(),
            'objectives' => "Practise conservation agriculture (minimum tillage, mulching) and record growth and pest pressure through the season.\nData supports comparisons with the archive's conservation agriculture studies.",
            'coordinator_id' => $ethel->id,
        ]);

        $vegetables = CropPlot::updateOrCreate(['name' => 'Garden Plot 4'], [
            'crop' => 'Tomato',
            'variety' => 'Tengeru 97',
            'location' => 'Irrigated garden',
            'area_m2' => 120,
            'season' => '2026 dry season',
            'status' => PlotStatus::GROWING,
            'planted_on' => now()->subDays(50)->toDateString(),
            'expected_harvest_on' => now()->addDays(20)->toDateString(),
            'objectives' => 'Year-round vegetable production under drip irrigation, monitoring Tuta absoluta pressure.',
            'coordinator_id' => $peter?->id ?? $ethel->id,
        ]);

        CropPlot::updateOrCreate(['name' => 'Block B · Plot 2'], [
            'crop' => 'Soybean',
            'location' => 'School farm, Block B',
            'area_m2' => 300,
            'season' => '2026/27 rain-fed',
            'status' => PlotStatus::PREPARING,
            'coordinator_id' => $ethel->id,
        ]);

        $recorders = array_values(array_filter([$lukundo, $kelvin, $peter, $ethel]));

        if ($maize->observations()->doesntExist()) {
            $maizeLog = [
                [35, FieldActivity::PLANTING, null, null, null, 'Planted at 75 cm x 25 cm, one seed per station, with basal D compound.'],
                [28, FieldActivity::MEASUREMENT, 'Emergence', 92, '%', 'Good emergence across the plot.'],
                [21, FieldActivity::WEEDING, null, null, null, 'First hand weeding; mulch topped up.'],
                [14, FieldActivity::MEASUREMENT, 'Plant height', 38, 'cm', null],
                [10, FieldActivity::SCOUTING, 'Fall armyworm incidence', 8, '%', 'Window-pane damage on outer rows; below action threshold.'],
                [7, FieldActivity::FERTILISING, 'Urea top dressing', 100, 'kg/ha', 'Applied after 22 mm of rain.'],
                [2, FieldActivity::MEASUREMENT, 'Plant height', 71, 'cm', null],
            ];

            foreach ($maizeLog as $i => [$daysAgo, $activity, $metric, $value, $unit, $notes]) {
                $maize->observations()->create([
                    'recorded_by' => $recorders[$i % count($recorders)]->id,
                    'observed_on' => now()->subDays($daysAgo)->toDateString(),
                    'activity' => $activity,
                    'metric' => $metric,
                    'value' => $value,
                    'unit' => $unit,
                    'notes' => $notes,
                ]);
            }
        }

        if ($vegetables->observations()->doesntExist()) {
            $vegetables->observations()->create([
                'recorded_by' => $recorders[0]->id,
                'observed_on' => now()->subDays(5)->toDateString(),
                'activity' => FieldActivity::SCOUTING,
                'metric' => 'Tuta absoluta trap catch',
                'value' => 14,
                'unit' => 'moths/trap/week',
                'notes' => 'Pheromone traps replaced. Mines visible on lower leaves.',
            ]);
        }
    }
}
