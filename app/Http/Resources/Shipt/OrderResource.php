<?php

namespace App\Http\Resources\Shipt;

use App\Enum\ShiptMethod;
use App\Http\Resources\CardInfoResource;
use App\Http\Resources\Items\ItemResource;
use App\Http\Resources\Stockpile\StockpileResource;
use App\Libs\CarbonFormatUtil;
use App\Services\Constant\GlobalConstant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\Constant\ShiptConstant as SC;

/**
 * 1件分の注文情報をまとめるResourceクラス。
 * @since 5.1.0
 */
class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $row = $this[GlobalConstant::DATA];
        $shiptData = $this[SC::ITEMS];
        // 商品価格の合計
        $productPrice = $this->collection($shiptData)->sum(function($item) {
            return $item[SC::PRODUCT_PRICE];
        });

        // クーポン割引額の合計
        $coupon = $this->collection($shiptData)->sum(function($item) {
            return $item[SC::DISCOUNT_AMOUNT];
        });

        // 商品価格の合計 - クーポン割引額の合計
        $totalPrice = $this->calcTotalPrice($productPrice, $coupon);
        // 送料
        $shiptFee = ShiptMethod::findByPrice($productPrice)->price;

        $shiptFeePerItems = round($shiptFee / count($shiptData));
        foreach ($shiptData as &$s) {
            $stock = $s[SC::STOCK];
            $subTotalPrice = $this->calcSubTotalPrice($s, $shiptFeePerItems);
            $items[] = [
                SC::STOCK => new ItemResource($stock),
                SC::SHIPMENT => $s[SC::SHIPMENT],
                SC::TOTAL_PRICE => $subTotalPrice,
                SC::SINGLE_PRICE => $this->calcSinglePrice($subTotalPrice, $s[SC::SHIPMENT]),
                SC::IS_REGISTERED => $s[SC::IS_REGISTERED],
            ];
        }
        return [
            SC::ORDER_ID => $this[SC::ORDER_ID],
            SC::BUYER => $row->buyer(),
            SC::ZIPCODE => $row->postal_code(),
            SC::ADDRESS => $row->address(),
            SC::FEE => $shiptFee,
            SC::TOTAL_PRICE => $totalPrice,
            SC::DISCOUNT_AMOUNT => $coupon,
            SC::ITEMS => $items,
        ];
    }

    /**
     * 支払い金額を算出する。
     *
     * @param integer $productPrice 商品価格
     * @param integer $discount クーポン割引額
     * @return integer 支払い金額
     */
    private function calcTotalPrice(int $productPrice, int $discount):int {
        return $productPrice - $discount;
    }

    /**
     * 単価を算出する。
     *
     * @param integer $totalPrice 支払い金額
     * @param integer $shipment 注文枚数
     * @return integer 単価
     */
    private function calcSinglePrice(int $subtotalPrice, int $shipment):int {
        return (int)round($subtotalPrice / $shipment);
    }

    /**
     * 各商品の小計を算出する。
     *
     * @param array $itemData 商品情報
     * @param integer $shiptFeePerItems 1商品あたりの送料
     * @return integer 小計
     */
    private function calcSubTotalPrice(array $itemData, int $shiptFeePerItems):int {
        return $itemData[SC::PRODUCT_PRICE] - $itemData[SC::DISCOUNT_AMOUNT] - $shiptFeePerItems;
    }
}
