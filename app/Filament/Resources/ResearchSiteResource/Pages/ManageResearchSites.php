<?php

declare(strict_types=1);

namespace App\Filament\Resources\ResearchSiteResource\Pages;

use App\Filament\Resources\ResearchSiteResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageResearchSites extends ManageRecords
{
    protected static string $resource = ResearchSiteResource::class;

    protected ?string $subheading = 'Set up the field, lab, school and community sites where students collect study data. Students log records on each site\'s page.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('New site'),
        ];
    }
}
