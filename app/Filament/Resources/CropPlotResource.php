<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\PlotStatus;
use App\Enums\ProjectStatus;
use App\Enums\UserRole;
use App\Filament\Resources\CropPlotResource\Pages;
use App\Models\CropPlot;
use App\Models\Project;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/**
 * Plots of the Continuous Crop Production Initiative.
 */
class CropPlotResource extends Resource
{
    protected static ?string $model = CropPlot::class;

    protected static ?string $navigationIcon = 'heroicon-o-sun';

    protected static ?string $navigationGroup = 'Research & Innovation Office';

    protected static ?string $navigationLabel = 'Crop production plots';

    protected static ?string $modelLabel = 'crop plot';

    protected static ?int $navigationSort = 4;

    public static function canViewAny(): bool
    {
        return auth()->user()?->managesResearchOffice() ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Grid::make(2)->schema([
                TextInput::make('name')->required()->maxLength(255)->placeholder('Block A · Plot 3'),
                TextInput::make('season')->required()->maxLength(255)->placeholder('2026/27 rain-fed'),
                TextInput::make('crop')->required()->maxLength(255),
                TextInput::make('variety')->maxLength(255),
                TextInput::make('location')->maxLength(255),
                TextInput::make('area_m2')->label('Area (m²)')->numeric()->minValue(0),
                Select::make('status')->options(PlotStatus::options())->default(PlotStatus::PREPARING->value)->required(),
                Select::make('coordinator_id')
                    ->label('Coordinator')
                    ->options(fn (): array => User::role([UserRole::RESEARCH_OFFICER->value, UserRole::SUPERVISOR->value])->pluck('name', 'id')->all())
                    ->searchable(),
                DatePicker::make('planted_on')->native(false),
                DatePicker::make('expected_harvest_on')->native(false)->afterOrEqual('planted_on'),
                Select::make('project_id')
                    ->label('Linked research project')
                    ->helperText('Optional: a student study that uses this plot\'s data.')
                    ->options(fn (): array => Project::query()
                        ->where('status', '!=', ProjectStatus::REJECTED->value)
                        ->orderBy('title')
                        ->pluck('title', 'id')
                        ->all())
                    ->searchable()
                    ->columnSpanFull(),
                Textarea::make('objectives')
                    ->label('Learning and research objectives')
                    ->rows(3)
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->weight('medium')
                    ->description(fn (CropPlot $record): string => $record->crop.($record->variety ? " · {$record->variety}" : '')),
                TextColumn::make('season')->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (PlotStatus $state): string => $state->label())
                    ->color(fn (PlotStatus $state): string => $state->color()),
                TextColumn::make('planted_on')->date('j M Y')->sortable(),
                TextColumn::make('observations_count')->counts('observations')->label('Field records')->alignCenter(),
                TextColumn::make('observations_max_observed_on')
                    ->max('observations', 'observed_on')
                    ->label('Last record')
                    ->date('j M Y')
                    ->placeholder('None yet'),
                TextColumn::make('coordinator.name')->label('Coordinator')->placeholder('Unassigned')->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(PlotStatus::options()),
            ])
            ->defaultSort('planted_on', 'desc')
            ->actions([
                Action::make('log')
                    ->label('Field log')
                    ->icon('heroicon-o-table-cells')
                    ->color('gray')
                    ->url(fn (CropPlot $record): string => route('innovation.plot', $record))
                    ->openUrlInNewTab(),
                ActionGroup::make([
                    Action::make('export')
                        ->label('Download CSV')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->url(fn (CropPlot $record): string => route('innovation.plot.export', $record)),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCropPlots::route('/'),
        ];
    }
}
