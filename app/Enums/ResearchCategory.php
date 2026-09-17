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
}
