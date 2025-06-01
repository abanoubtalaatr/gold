<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Branch;
use App\Models\GoldPiece;
use App\Models\OrderRental;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderRental>
 */
class OrderRentalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-30 days', '+30 days');
        $endDate = $this->faker->dateTimeBetween($startDate, '+60 days');

        return [
            'user_id' => User::factory(),
            'gold_piece_id' => GoldPiece::factory(),
            'branch_id' => Branch::factory(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_price' => $this->faker->randomFloat(2, 100, 2000),
            'status' => $this->faker->randomElement(OrderRental::statuses()),
            'type' => $this->faker->randomElement([OrderRental::RENT_TYPE, OrderRental::LEASE_TYPE]),
            'is_suspended' => $this->faker->boolean(20), // 20% chance of being suspended
        ];
    }

    /**
     * Indicate that the order is a rent type (user renting out their piece).
     */
    public function rentType(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => OrderRental::RENT_TYPE,
        ]);
    }

    /**
     * Indicate that the order is a lease type (user renting from store).
     */
    public function leaseType(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => OrderRental::LEASE_TYPE,
        ]);
    }

    /**
     * Indicate that the order is pending approval.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderRental::STATUS_PENDING_APPROVAL,
        ]);
    }

    /**
     * Indicate that the order is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderRental::STATUS_APPROVED,
        ]);
    }

    /**
     * Indicate that the order is currently rented.
     */
    public function rented(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderRental::STATUS_RENTED,
        ]);
    }

    /**
     * Indicate that the order is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderRental::STATUS_REJECTED,
        ]);
    }
} 