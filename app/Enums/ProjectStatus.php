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
}
