<?php

namespace Database\Factories;

use App\Models\ShippingLog;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Services\Constant\GlobalConstant as GCon;
use App\Services\Constant\StockpileHeader;

/**
 * 
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ShippingLog>
 */
class ShippingLogFactory extends Factory
{
    protected $model = ShippingLog::class;


    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            GCon::NAME => $this->faker->name(),
            'zip_code' => $this->faker->postcode,
            'address' => $this->faker->address,
            'shipping_date' => $this->faker->dateTimeBetween('-1 day'),
            'single_price' => $this->faker->numberBetween(1, 300),
            'total_price' => $this->faker->numberBetween(1, 600),
            'order_id' => $this->faker->unique()->numberBetween(1, 100),
            StockpileHeader::QUANTITY => 1,
        ];
    }
}
