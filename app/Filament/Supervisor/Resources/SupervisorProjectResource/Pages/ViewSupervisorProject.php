<?php

declare(strict_types=1);

namespace App\Filament\Supervisor\Resources\SupervisorProjectResource\Pages;

use App\Filament\Supervisor\Actions\EvaluateHeaderAction;
use App\Filament\Supervisor\Resources\SupervisorProjectResource;
use Filament\Resources\Pages\ViewRecord;

class ViewSupervisorProject extends ViewRecord
{
    protected static string $resource = SupervisorProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EvaluateHeaderAction::make(),
        ];
    }
}
