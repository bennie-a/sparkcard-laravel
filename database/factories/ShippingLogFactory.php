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

    /**
     * 1500円未満
     */
    public function createRecord(ShopPlatform $platform, Carbon $shiptDate,
                                                                        int $subtotal, string $orderId, int $count, array $buyerInfo): static
    {
        return $this->state(function (array $attributes) use ($orderId, $platform, $shiptDate, $subtotal, $count, $buyerInfo) {
            $fee = 0;
            if ($platform === ShopPlatform::MERCARI) {
                $fee = $this->getMercariFee($shiptDate, $subtotal);
            } else if($platform === ShopPlatform::BASE) {
                $fee = $this->getBaseFee($shiptDate, $subtotal);
            }
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

    private function getMercariFee(Carbon $shiptDate, int $subtotal) {
        $fee = 0;
        if ($subtotal >= 300 && $subtotal < 1500) {
            $fee = $this->fetchMiniletter($shiptDate);
        } else if ($subtotal >= 1500 && $subtotal < 10000) {
            $fee = $this->fetchClickPost($shiptDate);
        } else if ($subtotal >= 10000) {
            $fee = $this->fetchRegisterMail();
        }
        return $fee;
    }

    private function getBaseFee(Carbon $shiptDate, int $subtotal) {
        $fee = 0;
        if ($subtotal >= 185 && $subtotal < 4999) {
            $fee = $this->fetchMiniletter($shiptDate);
        } else {
            $fee = $this->fetchClickPost($shiptDate);
        }
        return $fee;
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
        return $this->fetchShiptPrice($isBefore, ShiptMethod::MINI);
    }

    /**
     * クリックポストの価格を取得する。
     *
     * @param Carbon $shiptDate
     * @return integer
     */
    private function fetchClickPost(Carbon $shiptDate): int
    {
        $initialDate = Carbon::parse('2026-09-30');
        $isBefore = $shiptDate->lte($initialDate);
        return $this->fetchShiptPrice($isBefore, ShiptMethod::CLICK);
    }

    /**
     * 簡易書留の価格を取得する。
     *
     * @return integer
     */
    private function fetchRegisterMail(): int
    {
        return $this->fetchShiptPrice(false, ShiptMethod::REGISTER_MAIL);
    }

    /**
     * 送料を取得する。
     *
     * @param boolean $deleted
     * @param ShiptMethod $method
     * @return integer
     */
    private function fetchShiptPrice(bool $deleted, ShiptMethod $method): int
    {
        $shipping = Shipping::where(GCon::NAME, $method->value)
                                ->where('deleted', $deleted)->first();
        return $shipping->price ?? 0;
    }
}
