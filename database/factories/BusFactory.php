<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bus>
 */
class BusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Bus ' . $this->faker->unique()->bothify('##??'),
            'license_plate' => strtoupper($this->faker->bothify('??-###-??')),
            'color' => $this->faker->safeColorName(),
            'total_seats' => (string) $this->faker->randomElement([20, 25, 30, 35, 40, 45, 50, 55, 60, 65, 70, 75, 80]),
            'available_seats' => (string) $this->faker->numberBetween(0, (int) $this->faker->randomElement([20, 25, 30, 35, 40, 45, 50, 55, 60, 65, 70, 75, 80])),
            'status' => 'available',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
