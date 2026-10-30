<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Where a challenge entry sits in the review panel's process.
 */
enum EntryStatus: string
{
    case SUBMITTED = 'submitted';
    case SHORTLISTED = 'shortlisted';
    case NOT_SHORTLISTED = 'not_shortlisted';
    case RECOGNISED = 'recognised';

    public function label(): string
    {
        return match ($this) {
            self::SUBMITTED => 'Submitted',
            self::SHORTLISTED => 'Shortlisted',
            self::NOT_SHORTLISTED => 'Not shortlisted',
            self::RECOGNISED => 'Recognised',
        };
    }

    /**
     * Filament colour token used for status badges.
     */
    public function color(): string
    {
        return match ($this) {
            self::SUBMITTED => 'primary',
            self::SHORTLISTED => 'warning',
            self::NOT_SHORTLISTED => 'gray',
            self::RECOGNISED => 'success',
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
     * Whether the entry is shown publicly as a shortlisted or recognised project.
     */
    public function isPublic(): bool
    {
        return in_array($this, [self::SHORTLISTED, self::RECOGNISED], true);
    }
}
