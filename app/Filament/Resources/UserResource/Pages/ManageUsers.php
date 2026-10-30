<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserResource\Pages;

use App\Enums\UserRole;
use App\Filament\Resources\UserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageUsers extends ManageRecords
{
    protected static string $resource = UserResource::class;

    protected ?string $subheading = 'Add staff and students, give each person only the roles they need, and sign in as someone to see what they see.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('New user')
                ->fillForm(['role' => UserRole::STUDENT->value, 'additional_roles' => []])
                ->using(fn (array $data) => UserResource::saveAccount(null, $data)),
        ];
    }
}
