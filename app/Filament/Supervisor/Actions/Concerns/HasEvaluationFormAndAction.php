<?php

declare(strict_types=1);

namespace App\Filament\Supervisor\Actions\Concerns;

use App\Enums\ProjectStatus;
use App\Enums\SimilarityCheckStatus;
use App\Jobs\AnalyzeProjectGapsJob;
use App\Jobs\RunSimilarityAnalysisJob;
use App\Models\Project;
use App\Models\SupervisorEvaluation;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\ViewField;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait HasEvaluationFormAndAction
{
    /**
     * Build the evaluation slide-over modal form schema.
     *
     * @return array<Component>
     */
    public static function getEvaluationFormSchema(): array
    {
        return [
            // 1. AI Similarity & Plagiarism Inspection Section
            Section::make('AI Similarity & Plagiarism Inspection')
                ->description('Automated multi-source similarity scoring and side-by-side manuscript comparison.')
                ->collapsible()
                ->headerActions([
                    FormAction::make('runSimilarityCheck')
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
                    ViewField::make('similarity_breakdown')
                        ->hiddenLabel()
                        ->view('filament.components.similarity-breakdown')
                        ->registerActions([
                            FormAction::make('runSimilarityCheck')
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
                ]),

            // 2. Evaluation Form Grid
            Section::make('Supervisor Evaluation & Rubric')
                ->description('Enter rubric marks, structured commentary, decision, and optional annotated markups.')
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('rubric_score')
                            ->label('Electronic Rubric Mark')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.5)
                            ->suffix('/ 100')
                            ->extraInputAttributes(['class' => 'text-right font-mono'])
                            ->required()
                            ->helperText('Passing rubric threshold is 50.0 / 100.'),

                        ToggleButtons::make('decision')
                            ->label('Milestone Transition Decision')
                            ->options(fn (?Project $record): array => static::decisionOptions($record))
                            ->colors([
                                'approve' => 'success',
                                'continue' => 'info',
                                'revisions' => 'warning',
                                'reject' => 'danger',
                            ])
                            ->icons([
                                'approve' => 'heroicon-m-check-circle',
                                'continue' => 'heroicon-m-forward',
                                'revisions' => 'heroicon-m-arrow-path',
                                'reject' => 'heroicon-m-x-circle',
                            ])
                            ->default('approve')
                            ->inline()
                            ->live()
                            ->required()
                            ->columnSpanFull(),
                    ]),

                    Textarea::make('feedback')
                        ->label('Supervisory Feedback / Required Corrections')
                        ->rows(5)
                        ->required(fn ($get): bool => in_array($get('decision'), ['revisions', 'reject'], true))
                        ->placeholder('Provide academic critique, methodological corrections, or praise for student execution...'),

                    FileUpload::make('document_markup_path')
                        ->label('Annotated Manuscript / Track Changes Upload (Optional)')
                        ->disk('secure_research')
                        ->directory('evaluations/markups')
                        ->acceptedFileTypes([
                            'application/pdf',
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                            'application/msword',
                        ])
                        ->maxSize(25600)
                        ->helperText('Attach corrected PDF or DOCX file for the student author (stored privately).'),
                ]),
        ];
    }

    /**
     * Decision choices offered for the project's current milestone.
     *
     * @return array<string, string>
     */
    public static function decisionOptions(?Project $record): array
    {
        if ($record !== null && in_array($record->status, [ProjectStatus::DRAFT, ProjectStatus::PROPOSAL_SUBMITTED], true)) {
            return [
                'approve' => 'Approve proposal',
                'revisions' => 'Request revisions',
                'reject' => 'Reject proposal',
            ];
        }

        return [
            'approve' => 'Approve final manuscript',
            'continue' => 'Accept milestone, continue supervision',
            'revisions' => 'Request revisions',
            'reject' => 'Reject manuscript',
        ];
    }

    /**
     * Map a supervisor decision onto the next workflow state.
     * Approving a proposal opens supervision; approving a manuscript completes the study.
     */
    public static function resolveTargetStatus(Project $record, string $decision): ProjectStatus
    {
        $isProposalStage = in_array($record->status, [ProjectStatus::DRAFT, ProjectStatus::PROPOSAL_SUBMITTED], true);

        return match ($decision) {
            'approve' => $isProposalStage ? ProjectStatus::UNDER_SUPERVISION : ProjectStatus::COMPLETED,
            'continue', 'revisions' => ProjectStatus::UNDER_SUPERVISION,
            'reject' => ProjectStatus::REJECTED,
            default => $record->status,
        };
    }

    /**
     * Execute the evaluation submission logic.
     */
    public static function executeEvaluation(Project $record, array $data): void
    {
        DB::transaction(function () use ($record, $data): void {
            $supervisorId = (int) Auth::id();
            $rubricScore = (float) $data['rubric_score'];
            $decision = (string) $data['decision'];
            $feedback = isset($data['feedback']) ? (string) $data['feedback'] : null;
            $markupPath = isset($data['document_markup_path']) ? (string) $data['document_markup_path'] : null;

            // 1. Record the supervisor evaluation entry
            SupervisorEvaluation::create([
                'project_id' => $record->id,
                'supervisor_id' => $supervisorId,
                'rubric_score' => $rubricScore,
                'feedback' => $feedback,
                'document_markup_path' => $markupPath,
            ]);

            // 2. Map decision to state transitions
            $targetStatus = static::resolveTargetStatus($record, $decision);

            $updatePayload = [
                'status' => $targetStatus->value,
            ];

            if ($targetStatus === ProjectStatus::COMPLETED && $record->completion_year === null) {
                $updatePayload['completion_year'] = (int) date('Y');
            }

            $record->update($updatePayload);

            // 3. Dispatch in-app / database notification to advisee student
            if ($record->student) {
                Notification::make()
                    ->title('Research Milestone Evaluated')
                    ->body("Your supervisor has reviewed '{$record->title}'. Decision: {$targetStatus->label()} with rubric mark: {$rubricScore}/100.")
                    ->color($targetStatus === ProjectStatus::COMPLETED ? 'success' : 'warning')
                    ->sendToDatabase($record->student);
            }
        });

        // 4. Completed studies feed the institutional research gap index
        if ($record->status === ProjectStatus::COMPLETED) {
            AnalyzeProjectGapsJob::dispatch($record);
        }

        // 5. Toast notification to supervisor
        Notification::make()
            ->title('Evaluation Recorded')
            ->body('Milestone review submitted and student advised.')
            ->success()
            ->send();
    }
}
