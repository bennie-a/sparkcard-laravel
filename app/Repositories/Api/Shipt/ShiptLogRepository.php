<?php

namespace App\Repositories\Api\Shipt;
use App\Models\Shipt\Orders;

/**
 * 注文情報に関するRepositoryクラス
 */
class ShiptLogRepository
{
    /**
     * 注文番号から注文情報を取得する。
     *
     * @param string $orderId
     * @return Orders|null
     */
    public function findByOrderId(string $orderId)
    {
        return Orders::query()->where('order_id', $orderId)->
                                                    with([
                                                        'orderItems.stockpile',
                                                        'orderItems.stockpile.cardinfo',
                                                        'orderItems.stockpile.cardinfo.expansion',
                                                        'orderItems.stockpile.cardinfo.promotype',
                                                        'orderItems.stockpile.cardinfo.foiltype',
                                                    ])->first();
    }
}
