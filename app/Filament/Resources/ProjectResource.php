<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\ProjectStatus;
use App\Enums\ResearchCategory;
use App\Enums\UserRole;
use App\Models\Project;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Research Repository';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Grid::make(2)->schema([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Select::make('category')
                    ->options(collect(ResearchCategory::cases())->mapWithKeys(
                        fn (ResearchCategory $c) => [$c->value => $c->label()]
                    ))
                    ->required(),

                Select::make('status')
                    ->options(collect(ProjectStatus::cases())->mapWithKeys(
                        fn (ProjectStatus $s) => [$s->value => $s->label()]
                    ))
                    ->required(),

                Select::make('student_id')
                    ->relationship('student', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Select::make('supervisor_id')
                    ->relationship('supervisor', 'name')
                    ->searchable()
                    ->preload()
                    ->nullable(),

                TextInput::make('completion_year')
                    ->numeric()
                    ->minValue(1990)
                    ->maxValue((int) date('Y') + 1)
                    ->nullable(),

                RichEditor::make('abstract')
                    ->required()
                    ->columnSpanFull(),

                FileUpload::make('full_document_path')
                    ->label('Manuscript (PDF/DOCX)')
                    ->disk('local')
                    ->directory('manuscripts')
                    ->acceptedFileTypes(['application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                    ->visible(fn () => auth()->user()?->hasAnyRole([
                        UserRole::ADMIN->value,
                        UserRole::DEAN->value,
                        UserRole::RESEARCH_DIRECTOR->value,
                        UserRole::SUPERVISOR->value,
                    ])),

                FileUpload::make('raw_data_path')
                    ->label('Raw Data Assets')
                    ->disk('local')
                    ->directory('raw_datasets')
                    ->visible(fn () => auth()->user()?->hasAnyRole([
                        UserRole::ADMIN->value,
                        UserRole::DEAN->value,
                        UserRole::RESEARCH_DIRECTOR->value,
                        UserRole::SUPERVISOR->value,
                    ])),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->limit(45)
                    ->sortable(),

                TextColumn::make('category')
                    ->badge()
                    ->sortable(),

                BadgeColumn::make('status')
                    ->colors([
                        'primary' => ProjectStatus::PROPOSAL_SUBMITTED->value,
                        'warning' => ProjectStatus::UNDER_SUPERVISION->value,
                        'info' => ProjectStatus::UNDER_REVIEW->value,
                        'success' => ProjectStatus::COMPLETED->value,
                        'danger' => ProjectStatus::REJECTED->value,
                        'secondary' => ProjectStatus::DRAFT->value,
                    ]),

                TextColumn::make('student.name')
                    ->label('Student')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('supervisor.name')
                    ->label('Supervisor')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('completion_year')
                    ->label('Year')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->options(collect(ResearchCategory::cases())->mapWithKeys(
                        fn (ResearchCategory $c) => [$c->value => $c->label()]
                    )),

                SelectFilter::make('status')
                    ->options(collect(ProjectStatus::cases())->mapWithKeys(
                        fn (ProjectStatus $s) => [$s->value => $s->label()]
                    )),

                SelectFilter::make('completion_year')
                    ->attribute('completion_year'),
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
