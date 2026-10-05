<?php

namespace App\Repositories\Api\Shipt;

use App\Enum\ShopPlatform;
use App\Enum\SortOrder;
use App\Models\Shipt\OrderItem;
use App\Models\Shipt\Orders;
use App\Models\Stockpile;
use App\Services\Constant\GlobalConstant as GC;
use App\Services\Constant\ShiptConstant as SC;
use App\Services\Shipt\ShiptStoreRow;
use Illuminate\Database\Eloquent\Collection;

/**
 * 注文情報に関するRepositoryクラス
 */
class ShiptLogRepository
{
    /**
     * 注文情報IDから注文情報を取得する。
     *
     * @param int $id 注文情報ID
     * @return Orders|null
     */
    public function find(int $id)
    {
        return Orders::query()->where('id', $id)->
                                                    with([
                                                        'shipping',
                                                        'orderItems' => fn($query) => $query->orderBy('id', SortOrder::ASC->value),
                                                        'orderItems.stockpile',
                                                        'orderItems.stockpile.cardinfo',
                                                        'orderItems.stockpile.cardinfo.expansion',
                                                        'orderItems.stockpile.cardinfo.promotype',
                                                        'orderItems.stockpile.cardinfo.foiltype',
                                                    ])->first();
    }

    /**
     * 注文情報と紐づいた商品情報が存在するか検証する。
     *
     * @param string $orderId
     * @param integer $stockId
     * @return bool
     */
    public function existsItem(string $orderId, int $stockId):bool
    {
        $exists = OrderItem::with('orders', function ($query) use ($orderId){
                                $query->where(SC::PLATFORM_ORDER_ID, $orderId);
                            })->where(SC::STOCK_ID, $stockId)->exists();

        return $exists;
    }

    /**
     * 検索条件に合致する注文情報を取得する。
     *
     * @param array $details
     * @return Collection
     */
    public function fetch(array $details):Collection
    {
        return Orders::query()->
                    when(isset($details[SC::BUYER]), function($query) use($details) {
                        return $query->where(SC::BUYER, $details[SC::BUYER]);
                    })
                    ->when(isset($details[SC::SHIPT_DATE]), function($query) use($details) {
                        return $query->where(SC::SHIPT_DATE, $details[SC::SHIPT_DATE]);
                    })
                    ->orderBy(GC::ID, SortOrder::ASC->value)->get();
    }

    /**
     * ordersテーブルにプラットフォームと注文番号が合致したレコード
     * が存在するか検証する。
     *
     * @param ShopPlatform $platform
     * @param string $orderId
     * @return Orders|null
     */
    public function findByOrderId(ShopPlatform $platform, string $orderId):Orders|null
    {
        return Orders::query()->where(SC::PLATFORM, $platform->value)
                            ->where(SC::PLATFORM_ORDER_ID, $orderId)
                            ->first();
    }

    /**
     * 購入者情報を1件登録する。
     *
     * @param ShiptStoreRow $row
     * @return Orders
     */
    public function createOrder(ShiptStoreRow $row):Orders
    {
        return Orders::create([
            SC::PLATFORM => $row->platform()->value,
            SC::PLATFORM_ORDER_ID => $row->order_id(),
            SC::BUYER => $row->buyer(),
            SC::ZIPCODE => $row->postal_code(),
            SC::ADDRESS => $row->address(),
            SC::SHIPT_DATE => $row->shipping_date(),
            SC::ITEM_SUBTOTAL => $row->item_subtotal(),
            'coupon_discount' => $row->discount_amount(),
            SC::GRAND_TOTAL => $row->grand_total(),
            SC::FEE_ID => $row->feeId(),
            SC::ITEM_COUNT => $row->item_count(),
        ]);
    }

    /**
     * 最新のレコードを取得する。
     */
    public function fetchLatestLog():Orders|null
    {
        return Orders::query()->orderBy(GC::ID, SortOrder::DESC->value)->first();
    }

    /**
     * order_itemテーブルに商品情報を1件登録し、
     * stockpileテーブルの在庫数を更新する。
     *
     * @param int $orderId
     * @param array $item
     * @return void
     */
    public function createOrderItem(int $orderId, array $item):void
    {
        OrderItem::create([
                                                SC::ORDER_ID => $orderId,
                                                SC::STOCK_ID => $item[GC::ID],
                                                SC::QUANTITY => $item[SC::SHIPMENT],
                                                SC::UNIT_PRICE => $item[SC::UNIT_PRICE],
                                                SC::SUBTOTAL => $item[SC::SUBTOTAL],
                                                ]);

        $stock = Stockpile::find($item[GC::ID]);
        $stock->quantity = $stock->quantity - $item[SC::SHIPMENT];
        $stock->update();
    }
}
