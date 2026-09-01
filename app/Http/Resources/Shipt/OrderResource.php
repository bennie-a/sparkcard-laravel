<?php

namespace App\Http\Resources\Shipt;

use App\Services\Constant\GlobalConstant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\Constant\ShiptConstant as SC;
use App\Services\Constant\ShiptConstant;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $fee = $this->shipping;
        return [
            GlobalConstant::ID => $this->id,
            SC::PLATFORM => $this->platform,
            SC::PLATFORM_ORDER_ID => $this->platform_order_id,
            SC::ZIPCODE => $this->zip_code,
            SC::ADDRESS => $this->address,
            SC::BUYER => $this->buyer_name,
            SC::SHIPPING_DATE => $this->shipt_date,
            SC::ITEM_COUNT => $this->item_count,
            SC::ITEM_SUBTOTAL => $this->items_subtotal,
            SC::DISCOUNT_AMOUNT => $this->coupon_discount,
            SC::GRAND_TOTAL => $this->grand_total,
            SC::FEE => [GlobalConstant::ID =>$fee->id, SC::METHOD => $fee->name, ShiptConstant::PRICE => $fee->price],
            // 'orderitems' => OrderItemResource::collection($this->whenLoaded('orderitems')),
            SC::PREV_ID => $this->previous()?->id ?? 0,
            SC::NEXT_ID => $this->next()?->id ?? 0,
        ];
    }
}
