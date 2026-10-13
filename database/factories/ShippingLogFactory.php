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
            StockpileHeader::QUANTITY => fake()->numberBetween(0, 10),
            'stock_id' => Stockpile::inRandomOrder()->first()->id
        ];
    }

         /**
     * 1500円未満
     */
    public function under1500(ShopPlatform $platform, Carbon $shiptDate, int $subtotal = 0): static
    {
        return $this->state(function (array $attributes) use ($platform, $shiptDate, $subtotal) {
            $max = 1499;
            if ($platform === ShopPlatform::BASE) {
                $max = 4999;
            }
            $itemSubtotal = $subtotal > 0 ? $subtotal : $this->faker->numberBetween(113, $max);
            $fee = $this->fetchMiniletter($shiptDate);
            $totalPrice = $itemSubtotal - $fee;
            return [
                'total_price' => $totalPrice,
                'order_id' => $this->generateOrderId($platform),
                'single_price' => round($totalPrice / $attributes[StockpileHeader::QUANTITY]),
            ];
        });
    }

    /**
     * プラットフォームに応じて注文番号をランダムに生成する。
     *
     * @return string
     */
    private function generateOrderId(ShopPlatform $platform): string
    {
        if ($platform === ShopPlatform::MERCARI) {
            return 'order_'.Str::random(22);
        }
        if ($platform === ShopPlatform::DIRECT) {
            return 'd2c_'.Str::random(22);
        }
        return Str::upper(Str::random(16));
    }

    /**
     * ミニレターの価格を取得する。
     *
     * @param Carbon $shiptDate
     * @return integer
     */
    private function fetchMiniletter(Carbon $shiptDate): int
    {
        $initialDate = Carbon::parse('2024-09-30');
        $isBefore = $shiptDate->lte($initialDate);
        $shipping = Shipping::where(GCon::NAME, ShiptMethod::MINI->value)
                                ->where('deleted', $isBefore)->first();
        return $shipping->price ?? 0;
    }
}
