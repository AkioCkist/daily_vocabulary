<?php

namespace App\Enums;

/**
 * Enum for test question types.
 */
enum QuestionType: string
{
    case WORD_TO_DEFINITION = 'word_to_definition';
    case DEFINITION_TO_WORD = 'definition_to_word';
    case WORD_TO_MEANING = 'word_to_meaning';
    case MEANING_TO_WORD = 'meaning_to_word';

    /**
     * Get all question types as array.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get question type description.
     */
    public function description(): string
    {
        return match ($this) {
            self::WORD_TO_DEFINITION => 'Choose the correct definition',
            self::DEFINITION_TO_WORD => 'Type the correct word',
            self::WORD_TO_MEANING => 'Choose the correct meaning',
            self::MEANING_TO_WORD => 'Type the word with this meaning',
        };
    }

    /**
     * Check if this question type requires multiple choice options.
     */
    public function requiresMultipleChoice(): bool
    {
        return match ($this) {
            self::WORD_TO_DEFINITION, self::WORD_TO_MEANING => true,
            self::DEFINITION_TO_WORD, self::MEANING_TO_WORD => false,
        };
    }

    /**
     * Check if this question type requires text input.
     */
    public function requiresTextInput(): bool
    {
        return !$this->requiresMultipleChoice();
    }
}