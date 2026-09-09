<?php

namespace App\Http\Resources\Shipt;

use App\Services\Constant\GlobalConstant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\Constant\ShiptConstant as SC;
use App\Services\Constant\ShiptConstant;

/**
 * 注文情報をJSON形式で整形するResourceクラス
 * @since 6.1.0
 */
class OrderResource extends JsonResource
{
    /**
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // $fee = $this->shipping;
        return [
            GlobalConstant::ID => $this->when($this->id() != -1, $this->id()),
            SC::PLATFORM => $this->platform(),
            SC::PLATFORM_ORDER_ID => $this->orderId(),
            SC::ZIPCODE => $this->zipcode(),
            // SC::ADDRESS => $this->address,
            // SC::BUYER => $this->buyer_name,
            // SC::SHIPT_DATE => $this->shipt_date,
            // SC::ITEM_COUNT => $this->item_count,
            // SC::ITEM_SUBTOTAL => $this->items_subtotal,
            // SC::DISCOUNT_AMOUNT => $this->coupon_discount,
            // SC::GRAND_TOTAL => $this->grand_total,
            // SC::FEE => [GlobalConstant::ID =>$fee->id, SC::METHOD => $fee->name, ShiptConstant::PRICE => $fee->price],
            // SC::PREV_ID => $this->previous()?->id ?? 0,
            // SC::NEXT_ID => $this->next()?->id ?? 0,
            // SC::ITEMS => OrderItemResource::collection($this->orderitems),
        ];
    }


    /**
     * 注文情報IDを取得する。
     *
     * @return int
     */
    protected function id():int
    {
        return $this->id;
    }

    /**
     * 注文情報のプラットフォームを返す
     *
     * @return string
     */
    protected function platform(): string
    {
        return $this->platform;
    }

    /**
     * 注文番号を返す。
     *
     * @return string
     */
    protected function orderId():string {
        return $this->platform_order_id;
    }

    /**
     * 郵便番号を返す。
     *
     * @return string
     */
    protected function zipcode():string
    {
        return $this->zip_code;
    }
}
