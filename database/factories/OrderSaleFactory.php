<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Branch;
use App\Models\OrderSale;
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
            'status' => $this->faker->randomElement(OrderSale::statuses()),
            'is_suspended' => $this->faker->boolean(15), // 15% chance of being suspended
        ];
    }

    /**
     * Indicate that the order is pending approval.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderSale::STATUS_PENDING_APPROVAL,
        ]);
    }

    /**
     * Indicate that the order is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderSale::STATUS_APPROVED,
        ]);
    }

    /**
     * Indicate that the order is sold.
     */
    public function sold(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderSale::STATUS_SOLD,
        ]);
    }

    /**
     * Indicate that the order is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderSale::STATUS_REJECTED,
        ]);
    }
}
