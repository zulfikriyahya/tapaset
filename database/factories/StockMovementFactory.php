<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockMovementFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'item_id' => Item::factory(),
            'movement_type' => fake()->word(),
            'quantity' => fake()->numberBetween(-10000, 10000),
            'from_location_id' => Location::factory(),
            'to_location_id' => Location::factory(),
            'reference_number' => fake()->word(),
            'reason' => fake()->text(),
            'performed_by' => User::factory()->create()->performed_by,
            'performed_at' => fake()->dateTime(),
        ];
    }
}
