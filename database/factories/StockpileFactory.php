<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Stockpile>
 */
class StockpileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
        'language' => fake()->randomElement(['JP', 'EN', 'IT', 'CS', 'CT']),
        'condition' => fake()->randomElement(['NM', 'NM-', 'EX+', 'EX', 'PLD']),
        'quantity' => fake()->numberBetween(0, 10)
        ];
    }
}
