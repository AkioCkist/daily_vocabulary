<?php

namespace Database\Factories;

use App\Models\DailyTest;
use App\Models\Word;
use App\Models\DailyTestItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DailyTestItem>
 */
class DailyTestItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'daily_test_id' => DailyTest::factory(),
            'word_id' => Word::factory(),
            'question_type' => $this->faker->randomElement(['word_to_definition', 'definition_to_word', 'word_to_meaning', 'meaning_to_word']),
            'options' => ['option1', 'option2', 'option3', 'option4'],
            'correct_answer' => 'option1',
            'result' => null,
        ];
    }
}
