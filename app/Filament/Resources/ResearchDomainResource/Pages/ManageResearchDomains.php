<?php

declare(strict_types=1);

namespace App\Filament\Resources\ResearchDomainResource\Pages;

use App\Filament\Resources\ResearchDomainResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageResearchDomains extends ManageRecords
{
    protected static string $resource = ResearchDomainResource::class;

    protected ?string $subheading = 'The thematic areas research gaps are filed under. Gap analysis adds areas as studies are completed; rename them or merge duplicates here.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('New thematic area'),
        ];
    }
}
