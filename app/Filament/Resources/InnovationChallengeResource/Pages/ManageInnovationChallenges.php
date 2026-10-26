<?php

declare(strict_types=1);

namespace App\Filament\Resources\InnovationChallengeResource\Pages;

use App\Filament\Resources\InnovationChallengeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageInnovationChallenges extends ManageRecords
{
    protected static string $resource = InnovationChallengeResource::class;

    protected ?string $subheading = 'Open a call each semester, set the deadline and presentation date, then follow entries through panel review.';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('New challenge call'),
        ];
    }
}
