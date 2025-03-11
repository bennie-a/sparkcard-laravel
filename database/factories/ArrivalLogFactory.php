<?php

namespace Database\Factories;

use App\Models\ArrivalLog;
use App\Models\Stockpile;
use App\Models\VendorType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ArrivalLogFactory extends Factory
{

    protected $model = ArrivalLog::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'stock_id' => fake()->unique(true)->numberBetween(1, 13),
            'cost' => fake()->numberBetween(1, 100),
            'quantity' => fake()->numberBetween(1, 10),
        ];
    }
}
