<?php

declare(strict_types=1);

namespace App\Enums;

enum ProjectStatus: string
{
    case DRAFT = 'draft';
    case PROPOSAL_SUBMITTED = 'proposal_submitted';
    case UNDER_SUPERVISION = 'under_supervision';
    case UNDER_REVIEW = 'under_review';
    case COMPLETED = 'completed';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::PROPOSAL_SUBMITTED => 'Proposal Submitted',
            self::UNDER_SUPERVISION => 'Under Supervision',
            self::UNDER_REVIEW => 'Under Review',
            self::COMPLETED => 'Completed',
            self::REJECTED => 'Rejected',
        };
    }

    public function isPubliclyVisible(): bool
    {
        return $this === self::COMPLETED;
    }

    /**
     * Whether the project is still moving through the supervision pipeline.
     */
    public function isActive(): bool
    {
        return ! in_array($this, [self::COMPLETED, self::REJECTED], true);
    }

    /**
     * Whether the project is waiting on a supervisor decision.
     */
    public function awaitsSupervisor(): bool
    {
        return in_array($this, [self::PROPOSAL_SUBMITTED, self::UNDER_REVIEW], true);
    }

    /**
     * Filament colour token used for status badges.
     */
    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::PROPOSAL_SUBMITTED => 'primary',
            self::UNDER_SUPERVISION => 'warning',
            self::UNDER_REVIEW => 'info',
            self::COMPLETED => 'success',
            self::REJECTED => 'danger',
        };
    }
}
