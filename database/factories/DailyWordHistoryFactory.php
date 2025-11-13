<?php

namespace Database\Factories;

use App\Models\DailyWordHistory;
use App\Models\Word;
use Illuminate\Database\Eloquent\Factories\Factory;

class DailyWordHistoryFactory extends Factory
{
    protected $model = DailyWordHistory::class;

    public function definition(): array
    {
        return [
            'word_id' => Word::factory(),
            'date' => $this->faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
        ];
    }

    public function today(): static
    {
        return $this->state(fn (array $attributes) => [
            'date' => now()->toDateString(),
        ]);
    }

    public function yesterday(): static
    {
        return $this->state(fn (array $attributes) => [
            'date' => now()->subDay()->toDateString(),
        ]);
    }
}
