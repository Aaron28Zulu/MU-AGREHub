<?php

declare(strict_types=1);

namespace App\Filament\Resources\CropPlotResource\Pages;

use App\Filament\Resources\CropPlotResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageCropPlots extends ManageRecords
{
    protected static string $resource = CropPlotResource::class;

    protected ?string $subheading = 'Set up plots for the continuous crop production initiative. Students log field records on each plot\'s page.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('New plot'),
        ];
    }
}
