<?php

namespace App\Repositories\Api\Shipt;
use App\Models\Shipt\Orders;

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
                                                        'orderItems.stockpile',
                                                        'orderItems.stockpile.cardinfo',
                                                        'orderItems.stockpile.cardinfo.expansion',
                                                        'orderItems.stockpile.cardinfo.promotype',
                                                        'orderItems.stockpile.cardinfo.foiltype',
                                                    ])->first();
    }
}
