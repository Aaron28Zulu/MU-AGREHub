<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: string
{
    case STUDENT = 'student';
    case RESEARCHER = 'researcher';
    case SUPERVISOR = 'supervisor';
    case ADMIN = 'admin';
    case DEAN = 'dean';
    case RESEARCH_DIRECTOR = 'research_director';

    public function label(): string
    {
        return match ($this) {
            self::STUDENT => 'Student',
            self::RESEARCHER => 'Student Researcher',
            self::SUPERVISOR => 'Academic Supervisor',
            self::ADMIN => 'System Administrator',
            self::DEAN => 'Dean of School',
            self::RESEARCH_DIRECTOR => 'Director of Research',
        };
    }
}
