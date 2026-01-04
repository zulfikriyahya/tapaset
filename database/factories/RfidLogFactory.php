<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Loan;
use App\Models\RfidCard;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class RfidLogFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'rfid_card_id' => RfidCard::factory(),
            'user_id' => User::factory(),
            'action' => fake()->word(),
            'status' => fake()->word(),
            'item_id' => Item::factory(),
            'loan_id' => Loan::factory(),
            'location' => fake()->word(),
            'ip_address' => fake()->word(),
            'user_agent' => fake()->text(),
            'response_message' => fake()->text(),
        ];
    }
}
