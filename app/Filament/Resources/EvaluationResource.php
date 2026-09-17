<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\UserRole;
use App\Models\SupervisorEvaluation;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EvaluationResource extends Resource
{
    protected static ?string $model = SupervisorEvaluation::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'Research Repository';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Grid::make(2)->schema([
                Select::make('project_id')
                    ->relationship('project', 'title')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpanFull(),

                Select::make('supervisor_id')
                    ->relationship('supervisor', 'name')
                    ->searchable()
                    ->default(auth()->id())
                    ->required(),

                TextInput::make('rubric_score')
                    ->label('Rubric Score (0 - 100)')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->required(),

                Textarea::make('feedback')
                    ->label('Supervisor Review Comments')
                    ->rows(4)
                    ->columnSpanFull(),

                FileUpload::make('document_markup_path')
                    ->label('Annotated Draft / Markup')
                    ->disk('local')
                    ->directory('evaluations')
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('project.title')
                    ->label('Project Title')
                    ->limit(50)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('supervisor.name')
                    ->label('Supervisor')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('rubric_score')
                    ->label('Score')
                    ->badge()
                    ->color(fn (float $state): string => match (true) {
                        $state >= 75.0 => 'success',
                        $state >= 50.0 => 'warning',
                        default => 'danger',
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Date Evaluated')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user && $user->hasRole(UserRole::SUPERVISOR->value)) {
            return $query->where('supervisor_id', $user->id);
        }

        return $query;
    }
}
