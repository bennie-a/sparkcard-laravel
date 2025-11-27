<?php

namespace App\Http\Resources\Shipt;

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
        $items = [];
        foreach ($shiptData as &$s) {
            $stock = $s[SC::STOCK];
            $items[] = [
                SC::STOCK => new ItemResource($stock),
                SC::SHIPMENT => $s[SC::SHIPMENT],
                SC::SINGLE_PRICE => $s[SC::SINGLE_PRICE],
                SC::SUBTOTAL_PRICE => $s[SC::SUBTOTAL_PRICE],
                SC::DISCOUNT_AMOUNT => $s[SC::DISCOUNT_AMOUNT]
            ];
        }
        return [
            SC::ORDER_ID => $this[SC::ORDER_ID],
            SC::BUYER => $row->buyer(),
            SC::SHIPPING_DATE => CarbonFormatUtil::toDateString($row->shipping_date()),
            SC::ZIPCODE => $row->postal_code(),
            SC::ADDRESS => $row->address(),
            SC::ITEMS => $items,
        ];
    }
}
