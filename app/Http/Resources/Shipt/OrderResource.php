<?php

namespace App\Http\Resources\Shipt;

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
        return [
            SC::ORDER_ID => $this[SC::ORDER_ID],
            SC::BUYER => $row->buyer(),
            SC::SHIPPING_DATE => CarbonFormatUtil::toDateString($row->shipping_date()),
            SC::ZIPCODE => $row->postal_code(),
            SC::ADDRESS => $row->address(),
            SC::ITEMS => $this[SC::ITEMS]
        ];
    }
}
