<?php

declare(strict_types=1);

namespace App\Enums;

enum ResearchCategory: string
{
    case CROP_SCIENCE = 'crop_science';
    case ANIMAL_SCIENCE = 'animal_science';
    case AGRIBUSINESS = 'agribusiness';
    case PLANT_PROTECTION = 'plant_protection';

    public function label(): string
    {
        return match ($this) {
            self::CROP_SCIENCE => 'Crop Science',
            self::ANIMAL_SCIENCE => 'Animal Science',
            self::AGRIBUSINESS => 'Agribusiness & Agricultural Economics',
            self::PLANT_PROTECTION => 'Plant Protection & Entomology',
        };
    }

    /**
     * One-line scope note shown when browsing subjects.
     */
    public function description(): string
    {
        return match ($this) {
            self::CROP_SCIENCE => 'Agronomy, soil fertility, crop physiology and breeding.',
            self::ANIMAL_SCIENCE => 'Livestock nutrition, health, genetics and production systems.',
            self::AGRIBUSINESS => 'Markets, value chains, farm economics and policy.',
            self::PLANT_PROTECTION => 'Pests, diseases, weeds and integrated management.',
        };
    }
}
