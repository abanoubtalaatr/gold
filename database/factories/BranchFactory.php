<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Branch>
 */
class BranchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'address' => $this->faker->address,
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
            'city_id' => City::factory(),
            'vendor_id' => User::factory(),
            'is_active' => $this->faker->boolean,
            'working_days' => $this->faker->randomElement(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']),
            'working_hours' => $this->faker->randomElement(['9:00 AM - 5:00 PM', '10:00 AM - 6:00 PM', '11:00 AM - 7:00 PM']),
        ];
    }
}
