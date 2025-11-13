<?php

namespace Database\Factories;

use App\Models\Word;
use Illuminate\Database\Eloquent\Factories\Factory;

class WordFactory extends Factory
{
    protected $model = Word::class;

    public function definition(): array
    {
        return [
            'word' => $this->faker->unique()->word(),
            'pronunciation' => '/' . $this->faker->word() . '/',
            'definition' => $this->faker->sentence(),
            'example' => $this->faker->sentence(),
            'source' => $this->faker->randomElement(['oxford', 'cambridge', 'merriam-webster']),
        ];
    }
}
