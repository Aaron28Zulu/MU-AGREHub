<?php

declare(strict_types=1);

namespace App\Filament\Resources\AssociationEventResource\Pages;

use App\Filament\Resources\AssociationEventResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageAssociationEvents extends ManageRecords
{
    protected static string $resource = AssociationEventResource::class;

    protected ?string $subheading = 'Plan debates, workshops and field visits, see who registered, and publish the outcomes afterwards.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('New event'),
        ];
    }
}
