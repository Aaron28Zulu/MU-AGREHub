<?php

declare(strict_types=1);

namespace App\Filament\Supervisor\Widgets;

use App\Enums\ProjectStatus;
use App\Filament\Supervisor\Resources\SupervisorProjectResource;
use App\Models\Project;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class ReviewQueueWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    /**
     * Office members without supervision duties have no review queue.
     */
    public static function canView(): bool
    {
        return auth()->user()?->isFaculty() ?? false;
    }

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Waiting for your review';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Project::query()
                    ->where('supervisor_id', Auth::id())
                    ->whereIn('status', [ProjectStatus::PROPOSAL_SUBMITTED->value, ProjectStatus::UNDER_REVIEW->value])
                    ->with(['student:id,name', 'latestManuscript', 'latestSimilarityReport'])
                    ->oldest('updated_at')
            )
            ->columns([
                TextColumn::make('title')
                    ->label('Study')
                    ->weight('medium')
                    ->wrap()
                    ->description(fn (Project $record): string => $record->student?->name ?? 'Student'),
                TextColumn::make('status')
                    ->label('Stage')
                    ->badge()
                    ->formatStateUsing(fn (ProjectStatus $state): string => $state === ProjectStatus::PROPOSAL_SUBMITTED ? 'Proposal' : 'Manuscript draft')
                    ->color(fn (ProjectStatus $state): string => $state->color()),
                TextColumn::make('latestManuscript.version')
                    ->label('Version')
                    ->placeholder('No file')
                    ->fontFamily('mono'),
                TextColumn::make('latestSimilarityReport.similarity_score')
                    ->label('Similarity')
                    ->placeholder('Pending')
                    ->formatStateUsing(fn ($state): string => number_format((float) $state, 1).'%')
                    ->badge()
                    ->color(fn ($state): string => (float) $state > 25 ? 'danger' : ((float) $state >= 15 ? 'warning' : 'success')),
                TextColumn::make('updated_at')
                    ->label('Waiting')
                    ->since()
                    ->color('gray'),
            ])
            ->actions([
                Action::make('open')
                    ->label('Open & review')
                    ->icon('heroicon-m-arrow-right')
                    ->iconPosition('after')
                    ->button()
                    ->size('sm')
                    ->url(fn (Project $record): string => SupervisorProjectResource::getUrl('view', ['record' => $record])),
            ])
            ->emptyStateIcon('heroicon-o-check-circle')
            ->emptyStateHeading('You\'re all caught up')
            ->emptyStateDescription('New proposals and drafts from your advisees will appear here.')
            ->paginated([5, 10])
            ->defaultPaginationPageOption(5);
    }
}
