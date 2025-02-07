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
            'stock_id' => Stockpile::inRandomOrder()->first()->id,
            'cost' => fake()->numberBetween(1, 100),
            'quantity' => fake()->randomBetween(1, 10),
            'arrival_date' => fake()->dateTimeBetween('-5days', '5days')->format('Y-m-d'),
            // 'vendor_type_id' => VendorType::inRandomOrder()->first()->id(),
        ];
    }
}
