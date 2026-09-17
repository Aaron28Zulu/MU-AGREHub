<?php

declare(strict_types=1);

namespace App\Filament\Supervisor\Actions;

use App\Filament\Supervisor\Actions\Concerns\HasEvaluationFormAndAction;
use App\Models\Project;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables\Actions\Action;

class EvaluateAction extends Action
{
    use HasEvaluationFormAndAction;

    public static function getDefaultName(): ?string
    {
        return 'review_submission';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Review Submission')
            ->icon('heroicon-o-clipboard-document-check')
            ->color('primary')
            ->slideOver()
            ->modalWidth(MaxWidth::FourExtraLarge)
            ->modalHeading(fn (Project $record): string => "Review Submission: {$record->title}")
            ->modalDescription(fn (Project $record): string => 'Advisee: '.($record->student?->name ?? 'Student Author').' | Current Milestone: '.$record->status->label())
            ->modalSubmitActionLabel('Submit Evaluation & Transition')
            ->form(static::getEvaluationFormSchema())
            ->action(function (Project $record, array $data): void {
                static::executeEvaluation($record, $data);
            });
    }
}
