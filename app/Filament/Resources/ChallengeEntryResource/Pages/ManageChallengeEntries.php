<?php

declare(strict_types=1);

namespace App\Filament\Resources\ChallengeEntryResource\Pages;

use App\Filament\Resources\ChallengeEntryResource;
use Filament\Resources\Pages\ManageRecords;

class ManageChallengeEntries extends ManageRecords
{
    protected static string $resource = ChallengeEntryResource::class;

    protected ?string $subheading = 'Score entries on relevance, innovation, feasibility and impact. The office shortlists, recognises and assigns mentors.';
}
