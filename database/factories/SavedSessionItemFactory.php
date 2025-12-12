<?php

namespace Database\Factories;

use App\Models\SavedSession;
use App\Models\Flashcard;
use App\Models\SavedSessionItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SavedSessionItem>
 */
class SavedSessionItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'saved_session_id' => SavedSession::factory(),
            'flashcard_id' => Flashcard::factory(),
            'position' => $this->faker->numberBetween(1, 100),
        ];
    }
}
