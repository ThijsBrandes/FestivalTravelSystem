<?php

namespace Database\Factories;

use App\Models\Festival;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
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
            'festival_id' => Festival::factory(),
            'booked_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'canceled']),
            'total_price' => $this->faker->numberBetween(10, 500),
            'ticket_quantity' => $this->faker->numberBetween(1, 10),
        ];
    }

}
