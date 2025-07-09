<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Festival>
 */
class FestivalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph(),
            'date' => $this->faker->date(),
            'location' => $this->faker->city(),
            'price' => $this->faker->randomFloat(2, 10, 150),
            'capacity' => $this->faker->numberBetween(50, 5000),
            'booked_tickets' => $this->faker->numberBetween(0, 500),
            'is_active' => $this->faker->boolean(),
            'images' => json_encode([$this->faker->imageUrl(), $this->faker->imageUrl()]),
        ];
    }

}
