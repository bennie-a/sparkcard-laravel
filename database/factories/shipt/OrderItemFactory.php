<?php

namespace Database\Factories\Shipt;

use App\Models\Shipt\Orders;
use App\Models\Stockpile;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Services\Constant\ShiptConstant as SCon;
use App\Services\Constant\StockpileHeader as StockCon;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shipt\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = $this->faker->numberBetween(50, 10000);
        $quantity = fake()->numberBetween(1, 10);
        return [
            SCon::STOCK_ID => Stockpile::query()->inRandomOrder()->first()->id,
            SCon::SUBTOTAL => $subtotal,
            SCon::UNIT_PRICE => round($subtotal / $quantity),
            StockCon::QUANTITY => $quantity,
            ];
    }
}
