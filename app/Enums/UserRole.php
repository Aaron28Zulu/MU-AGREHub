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
    case RESEARCH_OFFICER = 'research_officer';

    public function label(): string
    {
        return match ($this) {
            self::STUDENT => 'Student',
            self::RESEARCHER => 'Student Researcher',
            self::SUPERVISOR => 'Academic Supervisor',
            self::ADMIN => 'System Administrator',
            self::DEAN => 'Dean of School',
            self::RESEARCH_DIRECTOR => 'Director of Research',
            self::RESEARCH_OFFICER => 'Research & Innovation Office',
        };
    }

    /**
     * What the role may do, shown to the administrator when granting it.
     */
    public function description(): string
    {
        return match ($this) {
            self::STUDENT => 'Student portal: submits a proposal and their own manuscripts.',
            self::RESEARCHER => 'Student portal: runs a supervised study and submits milestones.',
            self::SUPERVISOR => 'Lecturer portal: reviews and grades only the students assigned to them.',
            self::ADMIN => 'Manages user accounts, roles and thematic areas, and can sign in as another user.',
            self::DEAN => 'Lecturer portal: oversees every project in the register and can grade or remove them.',
            self::RESEARCH_DIRECTOR => 'Oversees the project register and runs the Research & Innovation Office programmes.',
            self::RESEARCH_OFFICER => 'Office workspace: runs the challenge, events, research sites and thematic areas.',
        };
    }

    /**
     * Whether the role belongs to the student-facing portal.
     */
    public function isStudentRole(): bool
    {
        return in_array($this, [self::STUDENT, self::RESEARCHER], true);
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
