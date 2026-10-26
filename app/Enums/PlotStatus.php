<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Growth stage of a plot in the continuous crop production initiative.
 */
enum PlotStatus: string
{
    case PREPARING = 'preparing';
    case GROWING = 'growing';
    case HARVESTED = 'harvested';

    public function label(): string
    {
        return match ($this) {
            self::PREPARING => 'Land preparation',
            self::GROWING => 'In the field',
            self::HARVESTED => 'Harvested',
        };
    }

    /**
     * Filament colour token used for status badges.
     */
    public function color(): string
    {
        return match ($this) {
            self::PREPARING => 'gray',
            self::GROWING => 'success',
            self::HARVESTED => 'warning',
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
     * Whether students can still record field observations against the plot.
     */
    public function acceptsObservations(): bool
    {
        return $this !== self::HARVESTED;
    }
}
