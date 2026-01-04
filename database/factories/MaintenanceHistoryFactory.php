<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaintenanceHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'item_id' => Item::factory(),
            'maintenance_type' => fake()->word(),
            'description' => fake()->text(),
            'cost' => fake()->randomFloat(2, 0, 9999999999999.99),
            'performed_by' => fake()->word(),
            'performed_at' => fake()->dateTime(),
            'completed_at' => fake()->dateTime(),
            'status' => fake()->word(),
            'notes' => fake()->text(),
            'created_by' => User::factory()->create()->created_by,
        ];
    }
}
