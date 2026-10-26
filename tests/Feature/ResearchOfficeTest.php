<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\ChallengeStatus;
use App\Enums\EntryStatus;
use App\Enums\EntryType;
use App\Enums\EventType;
use App\Enums\FieldActivity;
use App\Enums\PlotStatus;
use App\Enums\ResearchCategory;
use App\Enums\UserRole;
use App\Filament\Resources\AssociationEventResource\Pages\ManageAssociationEvents;
use App\Filament\Resources\ChallengeEntryResource\Pages\ManageChallengeEntries;
use App\Filament\Resources\CropPlotResource\Pages\ManageCropPlots;
use App\Filament\Resources\InnovationChallengeResource\Pages\ManageInnovationChallenges;
use App\Livewire\Innovation\ChallengePage;
use App\Livewire\Innovation\EventPage;
use App\Livewire\Innovation\FieldPlotPage;
use App\Models\AssociationEvent;
use App\Models\ChallengeEntry;
use App\Models\CropPlot;
use App\Models\InnovationChallenge;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ResearchOfficeTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;

    protected User $officer;

    protected User $supervisor;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (UserRole::cases() as $role) {
            Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
        }

        $this->student = $this->makeUser('Kelvin Mwape', UserRole::STUDENT);
        $this->supervisor = $this->makeUser('Dr. C. Mulenga', UserRole::SUPERVISOR);

        // Office members are students who also hold the office role.
        $this->officer = $this->makeUser('Ethel Kunda', UserRole::RESEARCHER);
        $this->officer->assignRole(UserRole::RESEARCH_OFFICER->value);
    }

    protected function makeUser(string $name, UserRole $role): User
    {
        $user = User::factory()->create(['name' => $name, 'role' => $role]);
        $user->assignRole($role->value);

        return $user;
    }

    protected function makeChallenge(ChallengeStatus $status = ChallengeStatus::OPEN): InnovationChallenge
    {
        return InnovationChallenge::create([
            'title' => 'MUSAGRICA Vision 2030 Research and Innovation Challenge',
            'edition' => 'Semester 1, 2026/27',
            'description' => 'Call for research proposals and innovative project ideas.',
            'status' => $status,
            'opens_on' => now()->subWeek(),
            'closes_on' => now()->addWeek(),
        ]);
    }

    protected function makeEntry(InnovationChallenge $challenge, EntryStatus $status = EntryStatus::SUBMITTED, string $title = 'Solar drip kits for vegetable growers'): ChallengeEntry
    {
        return $challenge->entries()->create([
            'student_id' => $this->student->id,
            'title' => $title,
            'entry_type' => EntryType::INNOVATION,
            'category' => ResearchCategory::CROP_SCIENCE,
            'problem_statement' => str_repeat('Smallholders lack affordable irrigation. ', 3),
            'proposed_solution' => str_repeat('Build and test a low-cost solar drip kit. ', 3),
            'expected_impact' => 'Higher dry-season vegetable yields.',
            'status' => $status,
        ]);
    }

    public function test_innovation_hub_lists_the_offices_programmes_publicly(): void
    {
        $this->makeChallenge();
        $this->makeChallenge(ChallengeStatus::DRAFT)->update(['edition' => 'Unannounced draft call']);
        AssociationEvent::create([
            'title' => 'MUSAGRICA Agricultural Debate',
            'event_type' => EventType::DEBATE,
            'description' => 'Debate on agricultural transformation.',
            'starts_at' => now()->addDays(5),
        ]);
        CropPlot::create(['name' => 'Block A · Plot 1', 'crop' => 'Maize', 'season' => '2026/27', 'status' => PlotStatus::GROWING]);

        $this->get(route('innovation'))
            ->assertOk()
            ->assertSee('Vision 2030 Research &amp; Innovation Challenge', false)
            ->assertSee('Semester 1, 2026/27')
            ->assertDontSee('Unannounced draft call')
            ->assertSee('MUSAGRICA Agricultural Debate')
            ->assertSee('Block A · Plot 1');
    }

    public function test_draft_challenge_pages_are_not_reachable(): void
    {
        $challenge = $this->makeChallenge(ChallengeStatus::DRAFT);

        $this->get(route('innovation.challenge', $challenge))->assertNotFound();
    }

    public function test_student_can_enter_an_open_challenge_and_the_office_is_notified(): void
    {
        $challenge = $this->makeChallenge();

        Livewire::actingAs($this->student)
            ->test(ChallengePage::class, ['challenge' => $challenge])
            ->set('title', 'Solar drip kits for vegetable growers')
            ->set('entry_type', EntryType::INNOVATION->value)
            ->set('category', ResearchCategory::CROP_SCIENCE->value)
            ->set('problem_statement', str_repeat('Smallholders lack affordable irrigation. ', 3))
            ->set('proposed_solution', str_repeat('Build and test a low-cost solar drip kit. ', 3))
            ->set('expected_impact', 'Higher dry-season vegetable yields for smallholders.')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertRedirect(route('innovation.challenge', $challenge));

        $this->assertDatabaseHas('challenge_entries', [
            'innovation_challenge_id' => $challenge->id,
            'student_id' => $this->student->id,
            'status' => EntryStatus::SUBMITTED->value,
        ]);
        $this->assertSame(1, $this->officer->notifications()->count());
    }

    public function test_entries_are_refused_once_the_call_closes(): void
    {
        $challenge = $this->makeChallenge(ChallengeStatus::REVIEWING);

        Livewire::actingAs($this->student)
            ->test(ChallengePage::class, ['challenge' => $challenge])
            ->set('title', 'Solar drip kits for vegetable growers')
            ->call('submit')
            ->assertHasErrors('title');

        $this->assertDatabaseCount('challenge_entries', 0);
    }

    public function test_lecturers_cannot_submit_entries(): void
    {
        $challenge = $this->makeChallenge();

        Livewire::actingAs($this->supervisor)
            ->test(ChallengePage::class, ['challenge' => $challenge])
            ->call('submit')
            ->assertForbidden();
    }

    public function test_only_shortlisted_and_recognised_entries_are_shown_publicly(): void
    {
        $challenge = $this->makeChallenge();
        $this->makeEntry($challenge, EntryStatus::SUBMITTED, 'Still under review idea');
        $this->makeEntry($challenge, EntryStatus::NOT_SHORTLISTED, 'Not shortlisted idea');
        $this->makeEntry($challenge, EntryStatus::SHORTLISTED, 'Shortlisted cassava processing idea');

        $this->get(route('innovation.challenge', $challenge))
            ->assertOk()
            ->assertSee('Shortlisted cassava processing idea')
            ->assertDontSee('Still under review idea')
            ->assertDontSee('Not shortlisted idea');
    }

    public function test_panel_members_score_entries_and_the_mean_is_out_of_twenty(): void
    {
        $challenge = $this->makeChallenge(ChallengeStatus::REVIEWING);
        $entry = $this->makeEntry($challenge);

        $this->actingAs($this->supervisor);
        Filament::setCurrentPanel(Filament::getPanel('supervisor'));

        Livewire::test(ManageChallengeEntries::class)
            ->callTableAction('score', $entry, [
                'relevance_score' => 5,
                'innovation_score' => 4,
                'feasibility_score' => 3,
                'impact_score' => 4,
                'comments' => 'Promising, needs a costed budget.',
            ])
            ->assertHasNoTableActionErrors();

        $entry->reviews()->create([
            'reviewer_id' => $this->officer->id,
            'relevance_score' => 4,
            'innovation_score' => 4,
            'feasibility_score' => 4,
            'impact_score' => 2,
        ]);

        $this->assertSame(15.0, $entry->fresh()->panelScore());
    }

    public function test_students_cannot_score_their_own_entry_even_as_office_members(): void
    {
        $challenge = $this->makeChallenge(ChallengeStatus::REVIEWING);
        $entry = $challenge->entries()->create([
            'student_id' => $this->officer->id,
            'title' => 'Office member entry',
            'entry_type' => EntryType::RESEARCH_PROPOSAL,
            'category' => ResearchCategory::AGRIBUSINESS,
            'problem_statement' => 'Problem',
            'proposed_solution' => 'Solution',
            'expected_impact' => 'Impact',
        ]);

        $this->actingAs($this->officer);
        Filament::setCurrentPanel(Filament::getPanel('supervisor'));

        Livewire::test(ManageChallengeEntries::class)
            ->assertTableActionHidden('score', $entry);
    }

    public function test_office_decision_notifies_the_student(): void
    {
        $challenge = $this->makeChallenge(ChallengeStatus::REVIEWING);
        $entry = $this->makeEntry($challenge);

        $this->actingAs($this->officer);
        Filament::setCurrentPanel(Filament::getPanel('supervisor'));

        Livewire::test(ManageChallengeEntries::class)
            ->callTableAction('decide', $entry, [
                'status' => EntryStatus::RECOGNISED->value,
                'award' => 'Winner',
                'mentor_id' => $this->supervisor->id,
                'office_feedback' => 'Excellent proposal.',
            ])
            ->assertHasNoTableActionErrors();

        $entry->refresh();
        $this->assertSame(EntryStatus::RECOGNISED, $entry->status);
        $this->assertSame('Winner', $entry->award);
        $this->assertSame($this->supervisor->id, $entry->mentor_id);
        $this->assertSame(1, $this->student->notifications()->count());
    }

    public function test_lecturers_review_but_cannot_record_office_decisions(): void
    {
        $entry = $this->makeEntry($this->makeChallenge(ChallengeStatus::REVIEWING));

        $this->actingAs($this->supervisor);
        Filament::setCurrentPanel(Filament::getPanel('supervisor'));

        Livewire::test(ManageChallengeEntries::class)
            ->assertCanSeeTableRecords([$entry])
            ->assertTableActionVisible('score', $entry)
            ->assertTableActionHidden('decide', $entry);

        $this->get('/supervisor/innovation-challenges')->assertForbidden();
    }

    public function test_office_members_reach_the_office_workspace_but_not_supervision(): void
    {
        $this->actingAs($this->officer);

        $this->get('/supervisor')->assertOk()->assertSee('Research &amp; Innovation Office', false);
        $this->get('/supervisor/innovation-challenges')->assertOk();
        $this->get('/supervisor/association-events')->assertOk();
        $this->get('/supervisor/crop-plots')->assertOk();
        $this->get('/supervisor/supervisor-projects')->assertForbidden();
    }

    public function test_ordinary_students_cannot_open_the_office_workspace(): void
    {
        $this->actingAs($this->student)->get('/supervisor')->assertRedirect('/');
    }

    public function test_office_can_create_a_challenge_call_from_the_panel(): void
    {
        $this->actingAs($this->officer);
        Filament::setCurrentPanel(Filament::getPanel('supervisor'));

        Livewire::test(ManageInnovationChallenges::class)
            ->callAction('create', [
                'title' => 'MUSAGRICA Vision 2030 Research and Innovation Challenge',
                'edition' => 'Semester 2, 2026/27',
                'description' => 'Second call of the year.',
                'status' => ChallengeStatus::OPEN->value,
            ])
            ->assertHasNoActionErrors();

        $this->assertDatabaseHas('innovation_challenges', ['edition' => 'Semester 2, 2026/27', 'status' => 'open']);
    }

    public function test_debate_registration_records_side_and_respects_speaker_slots(): void
    {
        $debate = AssociationEvent::create([
            'title' => 'MUSAGRICA Agricultural Debate',
            'event_type' => EventType::DEBATE,
            'motion' => 'Are climate change and agricultural finance the only barriers?',
            'description' => 'Debate.',
            'starts_at' => now()->addDays(3),
            'speaker_slots' => 1,
        ]);

        Livewire::actingAs($this->student)
            ->test(EventPage::class, ['event' => $debate])
            ->set('wants_to_speak', true)
            ->call('signUp')
            ->assertHasErrors('position')
            ->set('position', 'opposition')
            ->call('signUp')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('event_registrations', [
            'association_event_id' => $debate->id,
            'user_id' => $this->student->id,
            'wants_to_speak' => true,
            'position' => 'opposition',
        ]);

        Livewire::actingAs($this->officer)
            ->test(EventPage::class, ['event' => $debate])
            ->set('wants_to_speak', true)
            ->set('position', 'proposition')
            ->call('signUp')
            ->assertHasErrors('registration');

        Livewire::actingAs($this->officer)
            ->test(EventPage::class, ['event' => $debate])
            ->call('signUp')
            ->assertHasNoErrors();

        $this->assertSame(2, $debate->attendees()->count());
    }

    public function test_full_events_refuse_new_registrations(): void
    {
        $workshop = AssociationEvent::create([
            'title' => 'Project ARIS Launch Workshop',
            'event_type' => EventType::WORKSHOP,
            'description' => 'Launch.',
            'starts_at' => now()->addDays(3),
            'capacity' => 1,
        ]);
        $workshop->attendees()->attach($this->officer->id);

        Livewire::actingAs($this->student)
            ->test(EventPage::class, ['event' => $workshop])
            ->call('signUp')
            ->assertHasErrors('registration');
    }

    public function test_office_records_event_outcomes(): void
    {
        $debate = AssociationEvent::create([
            'title' => 'MUSAGRICA Agricultural Debate',
            'event_type' => EventType::DEBATE,
            'description' => 'Debate.',
            'starts_at' => now()->subDay(),
        ]);

        $this->actingAs($this->officer);
        Filament::setCurrentPanel(Filament::getPanel('supervisor'));

        Livewire::test(ManageAssociationEvents::class)
            ->callTableAction('outcomes', $debate, ['outcomes' => 'Extension services and market access matter as much as finance.'])
            ->assertHasNoTableActionErrors();

        $this->get(route('innovation.event', $debate))
            ->assertOk()
            ->assertSee('Extension services and market access matter as much as finance.');
    }

    public function test_students_log_field_records_and_export_them_as_csv(): void
    {
        $plot = CropPlot::create([
            'name' => 'Block A · Plot 1',
            'crop' => 'Maize',
            'season' => '2026/27',
            'status' => PlotStatus::GROWING,
            'planted_on' => now()->subDays(20),
        ]);

        Livewire::actingAs($this->student)
            ->test(FieldPlotPage::class, ['plot' => $plot])
            ->set('activity', FieldActivity::MEASUREMENT->value)
            ->set('metric', 'Plant height')
            ->set('value', '42.5')
            ->set('unit', 'cm')
            ->call('record')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('plot_observations', [
            'crop_plot_id' => $plot->id,
            'recorded_by' => $this->student->id,
            'metric' => 'Plant height',
            'unit' => 'cm',
        ]);

        $csv = $this->actingAs($this->student)
            ->get(route('innovation.plot.export', $plot))
            ->assertOk()
            ->streamedContent();

        $this->assertStringContainsString('days_after_planting', $csv);
        $this->assertStringContainsString('"Plant height",42.50,cm', $csv);
        $this->assertStringContainsString('Kelvin Mwape', $csv);
    }

    public function test_field_records_need_a_note_or_measurement_and_harvested_plots_are_closed(): void
    {
        $plot = CropPlot::create(['name' => 'Garden Plot 4', 'crop' => 'Tomato', 'season' => '2026', 'status' => PlotStatus::GROWING]);

        Livewire::actingAs($this->student)
            ->test(FieldPlotPage::class, ['plot' => $plot])
            ->set('activity', FieldActivity::WEEDING->value)
            ->call('record')
            ->assertHasErrors('notes');

        $plot->update(['status' => PlotStatus::HARVESTED]);

        Livewire::actingAs($this->student)
            ->test(FieldPlotPage::class, ['plot' => $plot->fresh()])
            ->set('activity', FieldActivity::HARVEST->value)
            ->set('notes', 'Late record')
            ->call('record')
            ->assertHasErrors('activity');

        $this->assertDatabaseCount('plot_observations', 0);
    }

    public function test_guests_cannot_log_or_export_field_data(): void
    {
        $plot = CropPlot::create(['name' => 'Garden Plot 4', 'crop' => 'Tomato', 'season' => '2026', 'status' => PlotStatus::GROWING]);

        $this->get(route('innovation.plot.export', $plot))->assertRedirect(route('login'));

        Livewire::test(FieldPlotPage::class, ['plot' => $plot])
            ->set('activity', FieldActivity::WEEDING->value)
            ->set('notes', 'Weeded')
            ->call('record')
            ->assertForbidden();
    }

    public function test_office_can_add_a_plot_from_the_panel(): void
    {
        $this->actingAs($this->officer);
        Filament::setCurrentPanel(Filament::getPanel('supervisor'));

        Livewire::test(ManageCropPlots::class)
            ->callAction('create', [
                'name' => 'Block B · Plot 2',
                'season' => '2026/27 rain-fed',
                'crop' => 'Soybean',
                'status' => PlotStatus::PREPARING->value,
            ])
            ->assertHasNoActionErrors();

        $this->assertDatabaseHas('crop_plots', ['name' => 'Block B · Plot 2', 'crop' => 'Soybean']);
    }
}
