<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RfidCardFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'uid' => fake()->word(),
            'card_number' => fake()->word(),
            'is_active' => fake()->boolean(),
            'issued_at' => fake()->dateTime(),
            'expired_at' => fake()->dateTime(),
            'last_used_at' => fake()->dateTime(),
            'last_used_for' => fake()->word(),
            'failed_attempts' => fake()->numberBetween(-10000, 10000),
            'user_id' => User::factory(),
        ];
    }
}
