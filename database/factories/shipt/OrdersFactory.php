<?php

namespace Database\Factories\Shipt;

use App\Enum\ShiptMethod;
use App\Enum\ShopPlatform;
use App\Models\Shipping;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Services\Constant\ShiptConstant as SCon;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shipt\Orders>
 */
class OrdersFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $platform = $this->faker->randomElement(ShopPlatform::cases());
        $itemSubtotal = $this->faker->numberBetween(50, 10000);
        $coupon = $this->faker->randomElement([0, $this->faker->randomNumber(2)]);
        return [
            SCon::BUYER => $this->faker->name(),
            'platform' => $platform->value,
            'platform_order_id' => $this->createOrderId($platform),
            SCon::ZIPCODE => $this->faker->postcode1.'-'.$this->faker->postcode2,
            SCon::ADDRESS => $this->faker->prefecture.$this->faker->city.$this->faker->streetAddress.$this->faker->secondaryAddress,
            SCon::ITEM_COUNT => $this->faker->numberBetween(1, 10),
            'items_subtotal' => $itemSubtotal,
            'coupon_discount' => $coupon,
            'grand_total' => $itemSubtotal - $coupon,
            SCon::FEE_ID => $this->fetchShiptFeeId($itemSubtotal),
            SCon::SHIPPING_DATE => $this->faker->dateTimeBetween('-5 days', 'now'),
        ];
    }

     /**
     * 1500円未満
     */
    public function under1500(): static
    {
        return $this->state(function (array $attributes) {
            $itemSubtotal = $this->faker->numberBetween(50, 1499);

            return [
                SCon::ITEM_SUBTOTAL => $itemSubtotal,
                SCon::GRAND_TOTAL => $itemSubtotal - $attributes['coupon_discount'],
                SCon::FEE_ID => $this->fetchShiptFeeId($itemSubtotal),
            ];
        });
    }

    /**
     * 1500円以上、10000円未満
     */
    public function from1500To9999(): static
    {
        return $this->state(function (array $attributes) {
            $itemSubtotal = $this->faker->numberBetween(1500, 9999);

            return [
                SCon::ITEM_SUBTOTAL => $itemSubtotal,
                SCon::GRAND_TOTAL => $itemSubtotal - $attributes['coupon_discount'],
                SCon::FEE_ID => $this->fetchShiptFeeId($itemSubtotal),
            ];
        });
    }

    /**
     * 10000円以上
     */
    public function over10000(): static
    {
        return $this->state(function (array $attributes) {
            $itemSubtotal = $this->faker->numberBetween(10000, 30000);

            return [
                SCon::ITEM_SUBTOTAL => $itemSubtotal,
                SCon::GRAND_TOTAL => $itemSubtotal - $attributes['coupon_discount'],
                SCon::FEE_ID => $this->fetchShiptFeeId($itemSubtotal),
            ];
        });
    }

    /**
     * Undocumented function
     *
     * @param ShopPlatform $platform
     * @return string
     */
    private function createOrderId(ShopPlatform $platform): string
    {
        if ($platform === ShopPlatform::BASE) {
            return Str::upper(Str::random(16));
        }
        return 'order_' . Str::random(22);
    }

    /**
     * 合計額に応じて送料テーブルのIDを取得する。
     *
     * @param integer $itemSubtotal
     * @return int
     */
    private function fetchShiptFeeId(int $itemSubtotal): int
    {
        $shiptMethod = ShiptMethod::findByPrice($itemSubtotal);
        return $shiptMethod->id;
    }
}
