<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reward>
 */
class RewardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $discount = round($this->faker->randomFloat(0, 5, 50) / 5) * 5;

        return [
            'name' => $discount . '% Off',
            'description' => $this->faker->sentence(),
            'points_required' => $this->faker->numberBetween(50, 300),
            'discount_percentage' => $discount,
        ];
    }
}
