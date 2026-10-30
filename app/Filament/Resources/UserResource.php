<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\UserRole;
use App\Filament\Resources\UserResource\Pages;
use App\Http\Controllers\Auth\ImpersonationController;
use App\Models\User;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Staff and student accounts: the administrator adds people, sets their roles, deactivates or removes
 * them, and can sign in as them to see what they see.
 */
class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Administration';

    protected static ?string $navigationLabel = 'Users';

    protected static ?string $modelLabel = 'user';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function canViewAny(): bool
    {
        return auth()->user()?->canManageUsers() ?? false;
    }

    public static function canCreate(): bool
    {
        return static::canViewAny();
    }

    public static function canEdit(Model $record): bool
    {
        return static::canViewAny();
    }

    /**
     * Accounts that authored, graded or recorded research are deactivated instead, so nothing in the
     * archive is deleted with them. Nobody removes their own account.
     */
    public static function canDelete(Model $record): bool
    {
        return static::canViewAny() && ! $record->is(auth()->user()) && ! $record->hasResearchRecord();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Grid::make(2)->schema([
                TextInput::make('name')->required()->maxLength(255)->placeholder('Dr. A. Mwale'),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->placeholder('amwale@rris.mu.ac.zm'),
                TextInput::make('department')
                    ->label('Department or office')
                    ->maxLength(255)
                    ->placeholder('Agricultural Sciences'),
                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->minLength(8)
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->helperText(fn (string $operation): ?string => $operation === 'edit' ? 'Leave blank to keep the current password.' : null),
                Select::make('role')
                    ->label('Primary role')
                    ->options(UserRole::options())
                    ->required()
                    ->live()
                    ->helperText(fn (Get $get): ?string => UserRole::tryFrom((string) $get('role'))?->description())
                    // The administrator cannot demote themselves and lock the system out.
                    ->disabled(fn (?User $record): bool => $record?->is(auth()->user()) ?? false)
                    ->columnSpanFull(),
                CheckboxList::make('additional_roles')
                    ->label('Additional duties')
                    ->helperText('Give only what the person needs, for example a student who also serves on the Research & Innovation Office.')
                    ->options(fn (Get $get): array => collect(UserRole::cases())
                        ->reject(fn (UserRole $role): bool => $role->value === $get('role'))
                        ->mapWithKeys(fn (UserRole $role): array => [$role->value => $role->label()])
                        ->all())
                    ->descriptions(collect(UserRole::cases())->mapWithKeys(fn (UserRole $role): array => [$role->value => $role->description()])->all())
                    ->disabled(fn (?User $record): bool => $record?->is(auth()->user()) ?? false)
                    ->columns(2)
                    ->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with('roles'))
            ->columns([
                TextColumn::make('name')
                    ->searchable(['name', 'email'])
                    ->sortable()
                    ->weight('medium')
                    ->description(fn (User $record): string => $record->email),
                TextColumn::make('role')
                    ->label('Primary role')
                    ->badge()
                    ->formatStateUsing(fn (UserRole $state): string => $state->label())
                    ->color(fn (UserRole $state): string => $state->isStudentRole() ? 'gray' : 'primary'),
                TextColumn::make('extra_roles')
                    ->label('Additional duties')
                    ->state(fn (User $record): array => $record->roles
                        ->pluck('name')
                        ->reject(fn (string $name): bool => $name === $record->role?->value)
                        ->map(fn (string $name): string => UserRole::tryFrom($name)?->label() ?? $name)
                        ->values()
                        ->all())
                    ->badge()
                    ->color('warning')
                    ->placeholder('None'),
                TextColumn::make('department')->searchable()->placeholder('Not set')->toggleable(),
                TextColumn::make('deactivated_at')
                    ->label('Status')
                    ->badge()
                    ->state(fn (User $record): string => $record->isDeactivated() ? 'Deactivated' : 'Active')
                    ->color(fn (string $state): string => $state === 'Active' ? 'success' : 'danger'),
                TextColumn::make('created_at')->label('Added')->date('j M Y')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Role')
                    ->options(UserRole::options())
                    // Matches the primary role or any additional duty.
                    ->query(fn (Builder $query, array $data): Builder => filled($data['value'])
                        ? $query->whereHas('roles', fn (Builder $roles) => $roles->where('name', $data['value']))
                        : $query),
                TernaryFilter::make('active')
                    ->label('Status')
                    ->trueLabel('Active')
                    ->falseLabel('Deactivated')
                    ->queries(
                        true: fn (Builder $query): Builder => $query->whereNull('deactivated_at'),
                        false: fn (Builder $query): Builder => $query->whereNotNull('deactivated_at'),
                    ),
            ])
            ->defaultSort('name')
            ->actions([
                Action::make('impersonate')
                    ->label('Sign in as')
                    ->icon('heroicon-o-arrow-right-end-on-rectangle')
                    ->color('gray')
                    ->visible(fn (User $record): bool => auth()->user()->canImpersonate($record))
                    ->requiresConfirmation()
                    ->modalHeading(fn (User $record): string => "Sign in as {$record->name}?")
                    ->modalDescription('You will see the system exactly as they do, and anything you do is recorded as them. Use the banner at the top of the page to return to your account.')
                    ->modalSubmitActionLabel('Sign in as them')
                    ->action(fn (User $record) => redirect(ImpersonationController::start(auth()->user(), $record))),
                ActionGroup::make([
                    EditAction::make()
                        ->mutateRecordDataUsing(function (array $data, User $record): array {
                            $data['role'] = $record->role?->value;
                            $data['additional_roles'] = $record->roles->pluck('name')->reject(fn (string $name): bool => $name === $record->role?->value)->values()->all();

                            return $data;
                        })
                        ->using(fn (User $record, array $data): User => static::saveAccount($record, $data)),
                    Action::make('deactivate')
                        ->label('Deactivate')
                        ->icon('heroicon-o-no-symbol')
                        ->color('danger')
                        ->visible(fn (User $record): bool => ! $record->isDeactivated() && ! $record->is(auth()->user()))
                        ->requiresConfirmation()
                        ->modalDescription('They will be signed out and cannot sign in again until reactivated. Their research, grades and records stay in the archive.')
                        ->action(function (User $record): void {
                            $record->forceFill(['deactivated_at' => now()])->save();
                            Notification::make()->title("{$record->name} was deactivated")->success()->send();
                        }),
                    Action::make('reactivate')
                        ->label('Reactivate')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn (User $record): bool => $record->isDeactivated())
                        ->action(function (User $record): void {
                            $record->forceFill(['deactivated_at' => null])->save();
                            Notification::make()->title("{$record->name} can sign in again")->success()->send();
                        }),
                    DeleteAction::make()
                        ->modalDescription('This permanently removes the account. Only accounts with no research, grades or records can be removed.'),
                ]),
            ]);
    }

    /**
     * Creates or updates an account, keeping the primary role column and the granted roles in step.
     *
     * @param  array<string, mixed>  $data
     */
    public static function saveAccount(?User $record, array $data): User
    {
        $user = $record ?? new User;
        $isSelf = $record?->is(auth()->user()) ?? false;

        $user->fill(collect($data)->only(['name', 'email', 'department'])->all());

        // The model's hashed cast stores it securely.
        if (filled($data['password'] ?? null)) {
            $user->password = $data['password'];
        }

        $user->email_verified_at ??= now();
        $user->save();

        // Own roles are locked in the form; never let a stale submit change them.
        if (! $isSelf) {
            $user->assignAccountRoles(UserRole::from($data['role']), array_values($data['additional_roles'] ?? []));
        }

        return $user;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageUsers::route('/'),
        ];
    }
}
