<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserWord;
use App\Models\Word;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserWordFactory extends Factory
{
    protected $model = UserWord::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'word_id' => Word::factory(),
            'status' => $this->faker->randomElement(['saved', 'learned', 'reviewing']),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }

    public function saved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'saved',
        ]);
    }

    public function learned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'learned',
        ]);
    }

    public function reviewing(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'reviewing',
        ]);
    }
}
