<?php

declare(strict_types=1);

namespace App\Enums;

enum SimilarityCheckStatus: string
{
    case UNTESTED = 'untested';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case FAILED = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::UNTESTED => 'Untested',
            self::PROCESSING => 'Processing',
            self::COMPLETED => 'Completed',
            self::FAILED => 'Failed',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::UNTESTED => 'gray',
            self::PROCESSING => 'warning',
            self::COMPLETED => 'success',
            self::FAILED => 'danger',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::UNTESTED => 'heroicon-o-document-magnifying-glass',
            self::PROCESSING => 'heroicon-o-arrow-path',
            self::COMPLETED => 'heroicon-o-check-circle',
            self::FAILED => 'heroicon-o-exclamation-triangle',
        };
    }
}
