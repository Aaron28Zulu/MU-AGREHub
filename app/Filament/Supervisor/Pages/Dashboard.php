<?php

declare(strict_types=1);

namespace App\Filament\Supervisor\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\Support\Htmlable;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Overview';

    public function getHeading(): string|Htmlable
    {
        $name = str(auth()->user()?->name ?? '');

        // "Dr. C. Mulenga" becomes "Dr. Mulenga"; untitled names use the first name.
        $greetingName = $name->startsWith(['Dr. ', 'Prof. '])
            ? $name->before(' ').' '.$name->afterLast(' ')
            : $name->before(' ');

        return 'Good '.(now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening')).", {$greetingName}";
    }

    public function getSubheading(): ?string
    {
        return 'Submissions waiting on you, originality alerts and where the school\'s research is heading.';
    }

    /**
     * @return int|array<string, ?int>
     */
    public function getColumns(): int|array
    {
        return ['md' => 2, 'xl' => 2];
    }
}
