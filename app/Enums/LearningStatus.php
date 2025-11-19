<?php

namespace App\Enums;

/**
 * Enum for user word learning status.
 */
enum LearningStatus: string
{
    case NEW = 'new';
    case LEARNING = 'learning';
    case REVIEW = 'review';
    case MASTERED = 'mastered';

    /**
     * Get all learning statuses as array.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get status description.
     */
    public function description(): string
    {
        return match ($this) {
            self::NEW => 'Not yet encountered',
            self::LEARNING => 'Currently learning',
            self::REVIEW => 'Needs review',
            self::MASTERED => 'Fully mastered',
        };
    }

    /**
     * Get status color for UI.
     */
    public function color(): string
    {
        return match ($this) {
            self::NEW => 'gray',
            self::LEARNING => 'blue',
            self::REVIEW => 'yellow',
            self::MASTERED => 'green',
        };
    }
}