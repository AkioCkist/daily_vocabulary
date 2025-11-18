<?php

namespace App\Enums;

/**
 * Enum for CEFR language proficiency levels.
 */
enum CefrLevel: string
{
    case A1 = 'A1';
    case A2 = 'A2';
    case B1 = 'B1';
    case B2 = 'B2';
    case C1 = 'C1';
    case C2 = 'C2';

    /**
     * Get all CEFR levels as array.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get level description.
     */
    public function description(): string
    {
        return match ($this) {
            self::A1 => 'Beginner',
            self::A2 => 'Elementary',
            self::B1 => 'Intermediate',
            self::B2 => 'Upper Intermediate',
            self::C1 => 'Advanced',
            self::C2 => 'Proficient',
        };
    }

    /**
     * Get difficulty score (1-6, higher is more difficult).
     */
    public function difficultyScore(): int
    {
        return match ($this) {
            self::A1 => 1,
            self::A2 => 2,
            self::B1 => 3,
            self::B2 => 4,
            self::C1 => 5,
            self::C2 => 6,
        };
    }
}