<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'slug' => fake()->slug(),
            'is_consumable' => fake()->boolean(),
            'min_stock' => fake()->numberBetween(-10000, 10000),
            'description' => fake()->text(),
        ];
    }
}
