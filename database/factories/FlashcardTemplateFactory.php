<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\FlashcardTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FlashcardTemplate>
 */
class FlashcardTemplateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->word(),
            'settings' => [
                'word_count' => 20,
                'flashcard_type' => 'mixed',
            ],
        ];
    }
}
