<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Wallet>
 */
class WalletFactory extends Factory
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
            'balance' => $this->faker->randomFloat(2, 0, 10000),
            'pending_balance' => $this->faker->randomFloat(2, 0, 1000),
            'total_earned' => $this->faker->randomFloat(2, 0, 50000),
        ];
    }
}
