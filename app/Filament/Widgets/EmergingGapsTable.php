<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\ResearchGap;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class EmergingGapsTable extends BaseWidget
{
    protected static ?string $heading = 'Emerging Research Opportunities & Gaps';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ResearchGap::query()
                    ->where('is_emerging', true)
                    ->with([
                        'domain:id,name,slug',
                        'project:id,title,category,completion_year',
                    ])
                    ->latest('id')
            )
            ->columns([
                TextColumn::make('domain.name')
                    ->label('Thematic Domain')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('extracted_gap_text')
                    ->label('Identified Research Gap / Direction')
                    ->wrap()
                    ->searchable(),

                TextColumn::make('project.title')
                    ->label('Derived From Project')
                    ->limit(50)
                    ->tooltip(fn (ResearchGap $record): ?string => $record->project?->title)
                    ->searchable(),

                TextColumn::make('project.completion_year')
                    ->label('Year')
                    ->sortable(),

                IconColumn::make('is_saturated')
                    ->label('Domain Status')
                    ->boolean()
                    ->trueIcon('heroicon-s-fire')
                    ->falseIcon('heroicon-s-sparkles')
                    ->trueColor('warning')
                    ->falseColor('success')
                    ->tooltip(fn (ResearchGap $record): string => $record->is_saturated ? 'Saturated Domain' : 'Emerging Opportunity'),
            ])
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5);
    }
}
