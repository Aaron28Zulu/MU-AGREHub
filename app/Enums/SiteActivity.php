<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Type of activity or measurement logged against a research site.
 */
enum SiteActivity: string
{
    case SETUP = 'setup';
    case SAMPLING = 'sampling';
    case MEASUREMENT = 'measurement';
    case SURVEY = 'survey';
    case INTERVENTION = 'intervention';
    case MAINTENANCE = 'maintenance';
    case ANALYSIS = 'analysis';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::SETUP => 'Site set-up',
            self::SAMPLING => 'Sampling',
            self::MEASUREMENT => 'Measurement',
            self::SURVEY => 'Interview or survey',
            self::INTERVENTION => 'Treatment or intervention',
            self::MAINTENANCE => 'Maintenance',
            self::ANALYSIS => 'Analysis',
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
