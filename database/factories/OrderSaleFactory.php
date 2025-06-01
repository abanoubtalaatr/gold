<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Branch;
use App\Models\GoldPiece;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderSale>
 */
class OrderSaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'gold_piece_id' => GoldPiece::factory(),
            'branch_id' => Branch::factory(),
            'total_price' => $this->faker->randomFloat(2, 100, 1000),
            'is_suspended' => $this->faker->boolean,


            
        ];
    }
}
