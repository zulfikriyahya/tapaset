<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'item_code' => fake()->word(),
            'serial_number' => fake()->word(),
            'description' => fake()->text(),
            'purchase_date' => fake()->date(),
            'price' => fake()->randomFloat(2, 0, 9999999999999.99),
            'warranty_expired_at' => fake()->date(),
            'condition' => fake()->word(),
            'status' => fake()->word(),
            'quantity' => fake()->numberBetween(-10000, 10000),
            'min_quantity' => fake()->numberBetween(-10000, 10000),
            'location_id' => Location::factory(),
            'category_id' => Category::factory(),
            'image' => fake()->word(),
        ];
    }
}
