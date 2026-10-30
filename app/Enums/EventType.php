<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Activities organised by the Office of Research and Innovation.
 */
enum EventType: string
{
    case DEBATE = 'debate';
    case WORKSHOP = 'workshop';
    case SEMINAR = 'seminar';
    case FIELD_DAY = 'field_day';

    public function label(): string
    {
        return match ($this) {
            self::DEBATE => 'Debate',
            self::WORKSHOP => 'Workshop',
            self::SEMINAR => 'Seminar',
            self::FIELD_DAY => 'Field visit',
        };
    }

    /**
     * Filament colour token used for status badges.
     */
    public function color(): string
    {
        return match ($this) {
            self::DEBATE => 'warning',
            self::WORKSHOP => 'primary',
            self::SEMINAR => 'info',
            self::FIELD_DAY => 'success',
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
