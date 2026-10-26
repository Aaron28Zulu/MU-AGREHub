<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Kind of submission accepted by the innovation challenge.
 */
enum EntryType: string
{
    case RESEARCH_PROPOSAL = 'research_proposal';
    case INNOVATION = 'innovation';

    public function label(): string
    {
        return match ($this) {
            self::RESEARCH_PROPOSAL => 'Research proposal',
            self::INNOVATION => 'Innovative project',
        };
    }

    /**
     * Value => label map for select inputs.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $case): array => [$case->value => $case->label()])->all();
    }
}
