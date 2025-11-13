<?php

namespace Database\Factories;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition(): array
    {
        return [
            'email' => $this->faker->unique()->safeEmail(),
            'confirmed_at' => null,
            'unsubscribed_at' => null,
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'confirmed_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ]);
    }

    public function unsubscribed(): static
    {
        return $this->state(fn (array $attributes) => [
            'unsubscribed_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
        ]);
    }

    public function confirmedAndUnsubscribed(): static
    {
        return $this->state(fn (array $attributes) => [
            'confirmed_at' => $this->faker->dateTimeBetween('-30 days', '-8 days'),
            'unsubscribed_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
        ]);
    }
}
