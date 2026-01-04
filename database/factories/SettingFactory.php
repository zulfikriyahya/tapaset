<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SettingFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'key' => fake()->word(),
            'value' => fake()->text(),
            'type' => fake()->word(),
            'group' => fake()->word(),
            'description' => fake()->text(),
            'is_public' => fake()->boolean(),
        ];
    }
}
