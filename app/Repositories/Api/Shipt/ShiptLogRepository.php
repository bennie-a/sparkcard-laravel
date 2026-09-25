<?php

namespace App\Repositories\Api\Shipt;

use App\Enum\SortOrder;
use App\Models\Shipt\OrderItem;
use App\Models\Shipt\Orders;
use App\Services\Constant\GlobalConstant as GC;
use App\Services\Constant\ShiptConstant as SC;
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
        return Orders::query()->where(SC::SHIPT_DATE, $details[SC::SHIPT_DATE])
                                                ->with(['shipping'])
                                                ->orderBy(GC::ID, SortOrder::ASC->value)->get();
    }
}
