<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProjects extends ListRecords
{
    protected static string $resource = ProjectResource::class;

    protected ?string $subheading = 'Every registered study in the school, at every stage.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Register project'),
        ];
    }
}
