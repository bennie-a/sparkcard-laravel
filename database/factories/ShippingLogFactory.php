<?php

namespace Database\Factories;

use App\Enum\ShiptMethod;
use App\Enum\ShopPlatform;
use App\Models\Shipping;
use App\Models\ShippingLog;
use App\Models\Stockpile;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Services\Constant\GlobalConstant as GCon;
use App\Services\Constant\ShiptConstant as SCon;
use App\Services\Constant\StockpileHeader;
use Carbon\Carbon;
use Illuminate\Support\Str;

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
            'zip_code' => fake()->numerify('###-####'),
            'address' => fake()->prefecture().fake()->city().fake()->streetAddress(),
            'shipping_date' => $this->faker->dateTimeBetween('-3 years', 'now'),
            'single_price' => $this->faker->numberBetween(1, 300),
            'total_price' => $this->faker->numberBetween(1, 600),
            'order_id' => $this->faker->unique()->numberBetween(1, 100),
            StockpileHeader::QUANTITY => fake()->numberBetween(1, 10),
            'stock_id' => Stockpile::inRandomOrder()->first()->id
        ];
    }

    public function createRecord(int $fee, Carbon $shiptDate,
                                                                        int $subtotal, string $orderId, int $count, array $buyerInfo): static
    {
        return $this->state(function (array $attributes) use ($orderId, $fee, $shiptDate, $subtotal, $count, $buyerInfo) {
            $totalPrice = $subtotal - round($fee / $count);
            return [
                'total_price' => $totalPrice,
                'order_id' => $orderId,
                'single_price' => round($totalPrice / $attributes[StockpileHeader::QUANTITY]),
                GCon::NAME => $buyerInfo[SCon::BUYER],
                'zip_code' => $buyerInfo[SCon::POSTAL_CODE],
                'address' => $buyerInfo[SCon::STATE].$buyerInfo[SCon::CITY].
                                        $buyerInfo[SCon::ADDRESS_1].$buyerInfo[SCon::ADDRESS_2],
                'shipping_date' => $shiptDate,
            ];
        });
    }
}
