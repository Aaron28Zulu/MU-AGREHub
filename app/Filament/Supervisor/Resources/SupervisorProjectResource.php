<?php

declare(strict_types=1);

namespace App\Filament\Supervisor\Resources;

use App\Enums\ProjectStatus;
use App\Enums\ResearchCategory;
use App\Enums\SimilarityCheckStatus;
use App\Filament\Supervisor\Actions\EvaluateAction;
use App\Filament\Supervisor\Resources\SupervisorProjectResource\Pages;
use App\Jobs\RunSimilarityAnalysisJob;
use App\Models\Project;
use Filament\Forms\Form;
use Filament\Infolists\Components\Actions\Action as InfolistAction;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\TextEntry\TextEntrySize;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class SupervisorProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $modelLabel = 'Advisee Project';

    protected static ?string $pluralModelLabel = 'Advisee Projects';

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Supervision';

    protected static ?int $navigationSort = 1;

    /**
     * Strict Eloquent Query Scope:
     * Academic supervisors must ONLY see research projects assigned to them.
     * Prevents cross-supervisor data exposure at the Eloquent query layer.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('supervisor_id', Auth::id())
            ->with([
                'student:id,name,email,department',
                'latestSimilarityReport',
                'latestEvaluation',
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form;
    }

    /**
     * Read-Only Academic Infolist Schema for Advisee Project View
     */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // Phase 1: Header Section (Project Metadata)
                Section::make('Project Overview')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('title')
                            ->weight(FontWeight::Bold)
                            ->size(TextEntrySize::Large)
                            ->columnSpanFull(),

                        TextEntry::make('category')
                            ->label('Category')
                            ->badge()
                            ->formatStateUsing(fn ($state) => $state instanceof ResearchCategory ? $state->label() : ($state ? ResearchCategory::tryFrom((string) $state)?->label() : '—'))
                            ->color(fn ($state): string => match ($state instanceof ResearchCategory ? $state->value : (string) $state) {
                                'crop_science' => 'success',
                                'animal_science' => 'info',
                                'agribusiness' => 'warning',
                                'plant_protection' => 'primary',
                                default => 'gray',
                            }),

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn ($state): string => match ($state instanceof ProjectStatus ? $state->value : (string) $state) {
                                ProjectStatus::DRAFT->value => 'gray',
                                ProjectStatus::PROPOSAL_SUBMITTED->value => 'primary',
                                ProjectStatus::UNDER_SUPERVISION->value => 'warning',
                                ProjectStatus::UNDER_REVIEW->value => 'info',
                                ProjectStatus::COMPLETED->value => 'success',
                                ProjectStatus::REJECTED->value => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn ($state) => $state instanceof ProjectStatus ? $state->label() : ProjectStatus::tryFrom((string) $state)?->label() ?? (string) $state),

                        TextEntry::make('student.name')
                            ->label('Student Author')
                            ->icon('heroicon-m-user')
                            ->color('gray')
                            ->helperText(fn (Project $record): ?string => $record->student?->email ?? ($record->student?->department ?? null)),

                        TextEntry::make('completion_year')
                            ->label('Completion Year')
                            ->icon('heroicon-m-calendar')
                            ->color('gray')
                            ->placeholder('In Progress / Undefended'),
                    ]),

                // Phase 1: Abstract Section
                Section::make('Abstract & Objectives')
                    ->schema([
                        TextEntry::make('abstract')
                            ->prose()
                            ->columnSpanFull(),
                    ]),

                // Phase 2: Manuscript Viewer Section
                Section::make('Manuscript Document')
                    ->schema([
                        ViewEntry::make('document_preview')
                            ->hiddenLabel()
                            ->view('filament.components.document-viewer')
                            ->columnSpanFull(),
                    ]),

                // Phase 3: AI Similarity & Plagiarism Inspection
                static::getSimilarityInspectionSection(),
            ]);
    }

    /**
     * AI Similarity & Plagiarism Inspection Infolist Section definition for the EvaluateAction slide-over.
     */
    public static function getSimilarityInspectionSection(): Section
    {
        return Section::make('AI Similarity & Plagiarism Inspection')
            ->description('Automated multi-source similarity scoring and side-by-side manuscript comparison.')
            ->collapsible()
            ->headerActions([
                InfolistAction::make('runSimilarityCheck')
                    ->label(fn (?Project $record): string => $record?->similarity_check_status === SimilarityCheckStatus::COMPLETED ? 'Re-run Check' : 'Run AI Plagiarism Check')
                    ->icon('heroicon-m-sparkles')
                    ->color('primary')
                    ->visible(fn (?Project $record): bool => $record !== null && $record->similarity_check_status !== SimilarityCheckStatus::PROCESSING)
                    ->action(function (Project $record): void {
                        $record->update(['similarity_check_status' => SimilarityCheckStatus::PROCESSING]);
                        RunSimilarityAnalysisJob::dispatch($record);

                        Notification::make()
                            ->title('Similarity check dispatched')
                            ->body('Similarity check dispatched to background queue. This may take a minute.')
                            ->success()
                            ->send();
                    }),
            ])
            ->schema([
                ViewEntry::make('similarity_breakdown')
                    ->hiddenLabel()
                    ->view('filament.components.similarity-breakdown')
                    ->registerActions([
                        InfolistAction::make('runSimilarityCheck')
                            ->label(fn (?Project $record): string => $record?->similarity_check_status === SimilarityCheckStatus::COMPLETED ? 'Re-run Check' : 'Run AI Plagiarism Check')
                            ->icon('heroicon-m-sparkles')
                            ->color('primary')
                            ->visible(fn (?Project $record): bool => $record !== null && $record->similarity_check_status !== SimilarityCheckStatus::PROCESSING)
                            ->action(function (Project $record): void {
                                $record->update(['similarity_check_status' => SimilarityCheckStatus::PROCESSING]);
                                RunSimilarityAnalysisJob::dispatch($record);

                                Notification::make()
                                    ->title('Similarity check dispatched')
                                    ->body('Similarity check dispatched to background queue. This may take a minute.')
                                    ->success()
                                    ->send();
                            }),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // 1. Student Author & Identifier
                TextColumn::make('student.name')
                    ->label('Student')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Project $record): string => $record->student?->email ?? ($record->student?->department ?? 'Advisee')),

                // 2. Research Title (Truncated with tooltip)
                TextColumn::make('title')
                    ->label('Research Title')
                    ->searchable()
                    ->wrap()
                    ->limit(48)
                    ->tooltip(fn (Project $record): string => $record->title),

                // 3. Thematic Category Minimalist Badge
                TextColumn::make('category')
                    ->label('Category')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof ResearchCategory ? $state->label() : ($state ? ResearchCategory::tryFrom((string) $state)?->label() : '—'))
                    ->color(fn ($state): string => match ($state instanceof ResearchCategory ? $state->value : (string) $state) {
                        'crop_science' => 'success',
                        'animal_science' => 'info',
                        'agribusiness' => 'warning',
                        'plant_protection' => 'primary',
                        default => 'gray',
                    })
                    ->sortable(),

                // 4. Milestone Stage (ProjectStatus)
                TextColumn::make('status')
                    ->label('Milestone Stage')
                    ->badge()
                    ->color(fn ($state): string => match ($state instanceof ProjectStatus ? $state->value : (string) $state) {
                        ProjectStatus::DRAFT->value => 'gray',
                        ProjectStatus::PROPOSAL_SUBMITTED->value => 'primary',
                        ProjectStatus::UNDER_SUPERVISION->value => 'warning',
                        ProjectStatus::UNDER_REVIEW->value => 'info',
                        ProjectStatus::COMPLETED->value => 'success',
                        ProjectStatus::REJECTED->value => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => $state instanceof ProjectStatus ? $state->label() : ProjectStatus::tryFrom((string) $state)?->label() ?? (string) $state)
                    ->sortable(),

                // 5. Similarity Score Badge (<15% green, 15-25% amber, >25% red, Untested/Processing when null)
                TextColumn::make('latestSimilarityReport.similarity_score')
                    ->label('Similarity')
                    ->badge()
                    ->formatStateUsing(function ($state, Project $record): string {
                        if ($state === null) {
                            return match ($record->similarity_check_status) {
                                SimilarityCheckStatus::PROCESSING => 'Processing',
                                SimilarityCheckStatus::FAILED => 'Failed',
                                default => 'Untested',
                            };
                        }

                        return number_format((float) $state, 1).'%';
                    })
                    ->color(function ($state, Project $record): string {
                        if ($state === null) {
                            return match ($record->similarity_check_status) {
                                SimilarityCheckStatus::PROCESSING => 'warning',
                                SimilarityCheckStatus::FAILED => 'danger',
                                default => 'gray',
                            };
                        }

                        $score = (float) $state;
                        if ($score > 25.0) {
                            return 'danger';
                        }
                        if ($score >= 15.0) {
                            return 'warning';
                        }

                        return 'success';
                    })
                    ->sortable(),

                // 6. Last Updated Relative Time
                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                // Filter 1: Milestone Stage
                SelectFilter::make('status')
                    ->label('Milestone Stage')
                    ->options(collect(ProjectStatus::cases())->mapWithKeys(
                        fn (ProjectStatus $s) => [$s->value => $s->label()]
                    )),

                // Filter 2: Similarity Threshold Filter (> 20%)
                Filter::make('flagged_similarity')
                    ->label('Flagged for Review (≥ 20%)')
                    ->query(fn (Builder $query): Builder => $query->whereHas(
                        'latestSimilarityReport',
                        fn (Builder $sq): Builder => $sq->where('similarity_score', '>=', 20.0)
                    )),
            ])
            ->actions([
                // Sleek row action group with View and Review actions
                ActionGroup::make([
                    ViewAction::make(),
                    EvaluateAction::make('review_submission')
                        ->label('Review Submission'),
                ]),
            ])
            ->bulkActions([
                // Restricted bulk actions for academic supervisor safety
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSupervisorProjects::route('/'),
            'view' => Pages\ViewSupervisorProject::route('/{record}'),
        ];
    }
}
