<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Bus;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Trip>
 */
class TripFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $departure = $this->faker->dateTimeBetween('+1 day', '+1 week');

        return [
            'bus_id' => Bus::factory(),
            'user_id' => User::factory(),
            'starting_location' => $this->faker->city(),
            'destination' => $this->faker->city(),
            'departure_time' => $departure,
            'arrival_time' => (clone $departure)->modify('+3 hours'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
