<?php

namespace Database\Factories;

use App\Services\Constant\StockpileHeader;
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
        StockpileHeader::QUANTITY => fake()->numberBetween(0, 10)
        ];
    }
}
