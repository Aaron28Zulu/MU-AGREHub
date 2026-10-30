<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Subject (discipline) of a catalogued study, grouped by the school that teaches it.
 */
enum ResearchCategory: string
{
    case AGRICULTURE = 'agriculture';
    case ENVIRONMENTAL_SCIENCE = 'environmental_science';
    case BUSINESS_MANAGEMENT = 'business_management';
    case ACCOUNTING_FINANCE = 'accounting_finance';
    case ECONOMICS = 'economics';
    case EDUCATION = 'education';
    case HEALTH_SCIENCES = 'health_sciences';
    case COMPUTING = 'computing';
    case ENGINEERING = 'engineering';
    case NATURAL_SCIENCES = 'natural_sciences';
    case SOCIAL_SCIENCES = 'social_sciences';
    case PUBLIC_ADMINISTRATION = 'public_administration';

    public function label(): string
    {
        return match ($this) {
            self::AGRICULTURE => 'Agriculture & Food Systems',
            self::ENVIRONMENTAL_SCIENCE => 'Environment & Natural Resources',
            self::BUSINESS_MANAGEMENT => 'Business & Management',
            self::ACCOUNTING_FINANCE => 'Accounting & Finance',
            self::ECONOMICS => 'Economics',
            self::EDUCATION => 'Education',
            self::HEALTH_SCIENCES => 'Health Sciences',
            self::COMPUTING => 'Computing & Information Systems',
            self::ENGINEERING => 'Engineering',
            self::NATURAL_SCIENCES => 'Mathematics & Natural Sciences',
            self::SOCIAL_SCIENCES => 'Social Sciences',
            self::PUBLIC_ADMINISTRATION => 'Political Science & Public Administration',
        };
    }

    /**
     * One- or two-word name for dense tables; pair it with label() in a tooltip.
     */
    public function shortLabel(): string
    {
        return match ($this) {
            self::AGRICULTURE => 'Agriculture',
            self::ENVIRONMENTAL_SCIENCE => 'Environment',
            self::BUSINESS_MANAGEMENT => 'Business',
            self::ACCOUNTING_FINANCE => 'Accounting',
            self::ECONOMICS => 'Economics',
            self::EDUCATION => 'Education',
            self::HEALTH_SCIENCES => 'Health',
            self::COMPUTING => 'Computing',
            self::ENGINEERING => 'Engineering',
            self::NATURAL_SCIENCES => 'Sciences',
            self::SOCIAL_SCIENCES => 'Social Sciences',
            self::PUBLIC_ADMINISTRATION => 'Public Admin',
        };
    }

    /**
     * One-line scope note shown when browsing subjects.
     */
    public function description(): string
    {
        return match ($this) {
            self::AGRICULTURE => 'Crop and animal science, agribusiness and food security.',
            self::ENVIRONMENTAL_SCIENCE => 'Climate, water, forestry, land use and conservation.',
            self::BUSINESS_MANAGEMENT => 'Strategy, entrepreneurship, marketing and human resources.',
            self::ACCOUNTING_FINANCE => 'Financial reporting, auditing, banking and public finance.',
            self::ECONOMICS => 'Development, labour, trade and applied economic analysis.',
            self::EDUCATION => 'Teaching and learning, curriculum, inclusion and school leadership.',
            self::HEALTH_SCIENCES => 'Public health, nursing, clinical practice and health systems.',
            self::COMPUTING => 'Software, data, networks and digital services.',
            self::ENGINEERING => 'Civil, electrical, mechanical and renewable energy systems.',
            self::NATURAL_SCIENCES => 'Mathematics, statistics, physics, chemistry and biology.',
            self::SOCIAL_SCIENCES => 'Sociology, psychology, media and development studies.',
            self::PUBLIC_ADMINISTRATION => 'Governance, public policy, local government and law.',
        };
    }

    /**
     * School of the university that the subject belongs to.
     */
    public function school(): string
    {
        return match ($this) {
            self::AGRICULTURE, self::ENVIRONMENTAL_SCIENCE => 'Agriculture & Natural Resources',
            self::BUSINESS_MANAGEMENT, self::ACCOUNTING_FINANCE, self::ECONOMICS => 'Business Studies',
            self::EDUCATION => 'Education',
            self::HEALTH_SCIENCES => 'Medicine & Health Sciences',
            self::COMPUTING, self::ENGINEERING, self::NATURAL_SCIENCES => 'Science, Engineering & Technology',
            self::SOCIAL_SCIENCES, self::PUBLIC_ADMINISTRATION => 'Social Sciences',
        };
    }

    /**
     * Filament colour token used for subject badges, shared by subjects of the same school.
     */
    public function color(): string
    {
        return match ($this->school()) {
            'Agriculture & Natural Resources' => 'success',
            'Business Studies' => 'warning',
            'Medicine & Health Sciences' => 'danger',
            'Science, Engineering & Technology' => 'info',
            'Education' => 'primary',
            default => 'gray',
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
     * Subjects grouped under their school, for grouped select inputs and facet lists.
     *
     * @return array<string, list<self>>
     */
    public static function bySchool(): array
    {
        return collect(self::cases())->groupBy(fn (self $case): string => $case->school())->map->values()->map->all()->all();
    }
}
