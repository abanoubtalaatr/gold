<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GoldPiece>
 */
class GoldPieceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // check the gold piece Model elquent and fill this 
           'name' => $this->faker->name,
        'weight' => $this->faker->randomFloat(2, 0, 1000),
        'carat' => $this->faker->randomElement([18, 21, 24]),
            'rental_price_per_day' => $this->faker->randomFloat(2, 0, 1000),
            'sale_price' => $this->faker->randomFloat(2, 0, 1000),
            'deposit_amount' => $this->faker->randomFloat(2, 0, 1000),
            'branch_id' => Branch::factory(),
            'user_id' => User::factory(),    
            'type' => $this->faker->randomElement(['for_rent', 'for_sale']),
            'status' => $this->faker->randomElement(['available', 'rented', 'sold']),
            'qr_code' => $this->faker->uuid,
            'description' => $this->faker->sentence,
            'is_including_lobes' => $this->faker->boolean,
            
        ];
    }
}
