<?php

namespace Database\Factories;

use App\Enum\CardLanguage;
use App\Models\CardInfo;
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
            StockpileHeader::QUANTITY => fake()->numberBetween(0, 10),
            StockpileHeader::LANGUAGE => fake()->randomElement([CardLanguage::JP->value, CardLanguage::EN->value,
                                                                                                 CardLanguage::CT->value, CardLanguage::CS->value, CardLanguage::IT->value]),
            StockpileHeader::CONDITION => fake()->randomElement(['NM', 'NM-', 'EX', 'EX-', 'PLD']),
            StockpileHeader::CARD_ID => CardInfo::inRandomOrder()->first()->id,
        ];
    }
}
