<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Stage of a research site where students collect data for a study.
 */
enum SiteStatus: string
{
    case SETUP = 'setup';
    case ACTIVE = 'active';
    case CLOSED = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::SETUP => 'Setting up',
            self::ACTIVE => 'Collecting data',
            self::CLOSED => 'Closed',
        };
    }

    /**
     * Filament colour token used for status badges.
     */
    public function color(): string
    {
        return match ($this) {
            self::SETUP => 'gray',
            self::ACTIVE => 'success',
            self::CLOSED => 'warning',
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

    /**
     * Whether students can still record observations at the site.
     */
    public function acceptsObservations(): bool
    {
        return $this !== self::CLOSED;
    }
}
