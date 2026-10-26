<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Type of field activity or measurement logged against a crop plot.
 */
enum FieldActivity: string
{
    case LAND_PREPARATION = 'land_preparation';
    case PLANTING = 'planting';
    case FERTILISING = 'fertilising';
    case IRRIGATION = 'irrigation';
    case WEEDING = 'weeding';
    case SCOUTING = 'scouting';
    case MEASUREMENT = 'measurement';
    case HARVEST = 'harvest';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::LAND_PREPARATION => 'Land preparation',
            self::PLANTING => 'Planting',
            self::FERTILISING => 'Fertiliser application',
            self::IRRIGATION => 'Irrigation',
            self::WEEDING => 'Weeding',
            self::SCOUTING => 'Pest & disease scouting',
            self::MEASUREMENT => 'Growth measurement',
            self::HARVEST => 'Harvest & yield',
            self::OTHER => 'Other',
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
