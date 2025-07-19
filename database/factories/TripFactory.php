<?php

namespace Database\Factories;

use App\Models\Festival;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Bus;

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
            'festival_id' => Festival::factory(),
            'starting_location' => $this->faker->city(),
            'destination' => $this->faker->city(),
            'departure_time' => $departure,
            'arrival_time' => (clone $departure)->modify('+3 hours'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
