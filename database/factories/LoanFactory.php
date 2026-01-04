<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LoanFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'loan_number' => fake()->word(),
            'user_id' => User::factory(),
            'item_id' => Item::factory(),
            'loan_date' => fake()->dateTime(),
            'due_date' => fake()->dateTime(),
            'return_date' => fake()->dateTime(),
            'returned_condition' => fake()->word(),
            'status' => fake()->word(),
            'loan_notes' => fake()->text(),
            'return_notes' => fake()->text(),
            'created_by' => User::factory()->create()->created_by,
            'returned_by' => User::factory()->create()->returned_by,
            'approved_by' => User::factory()->create()->approved_by,
            'penalty_amount' => fake()->randomFloat(2, 0, 9999999999999.99),
            'is_paid' => fake()->boolean(),
        ];
    }
}
