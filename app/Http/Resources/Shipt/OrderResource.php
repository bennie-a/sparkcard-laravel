<?php

namespace App\Http\Resources\Shipt;

use App\Models\Shipping;
use App\Services\Constant\GlobalConstant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\Constant\ShiptConstant as SC;
use App\Services\Constant\ShiptConstant;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

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
        $fee = $this->fee();
        return [
            GlobalConstant::ID => $this->when($this->id() != -1, $this->id()),
            SC::PLATFORM => $this->platform(),
            SC::PLATFORM_ORDER_ID => $this->orderId(),
            SC::ZIPCODE => $this->zipcode(),
            SC::ADDRESS => $this->address(),
            SC::BUYER => $this->buyer(),
            SC::SHIPT_DATE => $this->when(!empty($this->shiptDate()), $this->shiptDate()),
            SC::ITEM_COUNT => $this->itemCount(),
            SC::ITEM_SUBTOTAL => $this->itemsSubtotal(),
            SC::DISCOUNT_AMOUNT => $this->couponDiscount(),
            SC::GRAND_TOTAL => $this->grandTotal(),
            SC::FEE => [GlobalConstant::ID =>$fee->id, SC::METHOD => $fee->name, SC::PRICE => $fee->price],
            SC::PREV_ID => $this->when($this->prevId() != -1, $this->prevId()),
            SC::NEXT_ID => $this->when($this->nextId() != -1, $this->nextId()),
            SC::ITEMS => $this->orderItems()
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

    /**
     * 住所を返す。
     *
     * @return string
     */
    protected function address():string
    {
        return $this->address;
    }

    /**
     * 購入者名を返す。
     *
     * @return string
     */
    protected function buyer():string
    {
        return $this->buyer_name;
    }

    /**
     * 発送日を返す。
     *
     * @return string
     */
    protected function shiptDate():string
    {
        return $this->shipt_date;
    }

    /**
     * 商品数を返す。
     *
     * @return int
     */
    protected function itemCount():int
    {
        return $this->item_count;
    }

    /**
     * 商品の合計金額（割引前）を返す。
     */
    protected function itemsSubtotal():int
    {
        return $this->items_subtotal;
    }

    /**
     * クーポン割引合計額を返す。
     *
     * @return integer
     */
    protected function couponDiscount():int
    {
        return $this->coupon_discount;
    }

    /**
     * 最終請求金額を返す。
     * @return  integer
     */
    protected function grandTotal():int
    {
        return $this->grand_total;
    }

    /**
     * 1つ前の注文情報IDを返す。
     *
     * @return integer
     */
    protected function prevId():int
    {
        return $this->previous()?->id ?? 0;
    }

        /**
     * 1つ後の注文情報IDを返す。
     *
     * @return integer
     */
    protected function nextId():int
    {
        return $this->next()?->id ?? 0;
    }

    /**
     * 送料に関するレコードを返す。
     *
     * @return Shipping
     */
    protected function fee():Shipping
    {
        return $this->fee;
    }

    /**
     * 商品情報を返す。
     *
     * @return AnonymousResourceCollection
     */
    protected function orderItems():AnonymousResourceCollection
    {
        return OrderItemResource::collection($this->orderitems);
    }
}
