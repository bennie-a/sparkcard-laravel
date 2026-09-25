<?php

namespace App\Http\Resources\Shipt;

use App\Enum\ShiptMethod;
use App\Enum\ShopPlatform;
use App\Http\Resources\CardInfoResource;
use App\Http\Resources\Items\ItemResource;
use App\Http\Resources\Stockpile\StockpileResource;
use App\Libs\CarbonFormatUtil;
use App\Models\Shipping;
use App\Services\Constant\GlobalConstant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\Constant\ShiptConstant as SC;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Override;

/**
 * CSVファイルの内容から1件分の注文情報をまとめるResourceクラス。
 * @since 5.1.0
 */
class CsvOrderResource extends OrderResource
{
    protected function id():int
    {
        return -1;
    }

    #[Override]
    protected function platform(): string
    {
        return ShopPlatform::MERCARI->value;
    }

    #[Override]
    protected function orderId():string {
        return  $this[SC::ORDER_ID];
    }

    #[Override]
    protected function zipcode():string
    {
        $row = $this[GlobalConstant::DATA];
        return $row->postal_code();
    }

    #[Override]
    protected function address():string
    {
        $row = $this[GlobalConstant::DATA];
        return $row->address();
    }

    #[Override]
    protected function buyer():string
    {
        $row = $this[GlobalConstant::DATA];
        return  $row->buyer();
    }

    #[Override]
    protected function shiptDate():string
    {
        return '';
    }

    #[Override]
    protected function itemCount():int
    {
        $items = $this[SC::ITEMS];
        return count($items);
    }

    #[Override]
    protected function itemsSubtotal():int
    {
        $items = $this[SC::ITEMS];
        $productPrice = $this->collection($items)->sum(function($item) {
            return $item[SC::PRODUCT_PRICE];
        });
        return $productPrice;
    }

    #[Override]
    protected function couponDiscount():int
    {
        $items = $this[SC::ITEMS];
        $coupon = $this->collection($items)->sum(function($item) {
            return $item[SC::DISCOUNT_AMOUNT];
        });
        return $coupon;
    }

    #[Override]
    protected function grandTotal():int
    {
        return $this->itemsSubtotal() - $this->couponDiscount();
    }

    #[Override]
    protected function prevId():int
    {
        return -1;
    }

    #[Override]
    protected function nextId():int
    {
        return -1;
    }

    #[Override]
    protected function fee():Shipping
    {
        $shiptFee = ShiptMethod::findByPrice($this->itemsSubtotal());
        return $shiptFee;
    }

    #[Override]
    protected function orderItems():AnonymousResourceCollection
    {
        $itemCount = $this->itemCount();
        $fee = $this->fee();
        $feePerItem = $itemCount > 0 ? round($fee->price / $itemCount) : 0;

        $items = array_map(function($item) use ($feePerItem) {
            $item[SC::PRODUCT_PRICE] -= $feePerItem;
            return $item;
        }, $this[SC::ITEMS]);

        return CsvOrderItemResource::collection($items);
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
