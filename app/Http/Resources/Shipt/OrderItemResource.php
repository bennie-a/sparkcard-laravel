<?php

namespace App\Http\Resources\Shipt;

use App\Http\Resources\Items\ItemResource;
use App\Services\Constant\GlobalConstant;
use App\Services\Constant\StockpileHeader;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\Constant\ShiptConstant as SC;

/**
 * 注文情報に含まれる商品情報1件分をJSON形式で
 * 整形するResourceクラス
 *
 * @since 6.1.0
 */
class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            GlobalConstant::ID => $this->when($this->id() != -1, $this->id()),
            SC::SHIPMENT => $this->shipment(),
            // SC::UNIT_PRICE => $this->unit_price,
            // SC::SUBTOTAL => $this->subtotal,
            // SC::STOCK => ItemResource::make($this->stockpile),
        ];
    }

    /**
     * 商品情報IDを返す。
     *
     * @return integer
     */
    protected function id():int
    {
        return $this->id;
    }

    /**
     * 注文枚数を返す。
     *
     * @return integer
     */
    protected function shipment():int
    {
        return $this->quantity;
    }
}
