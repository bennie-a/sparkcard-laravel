<?php

namespace App\Http\Resources\Shipt;

use App\Http\Resources\Shipt\OrderItemResource;
use App\Services\Constant\ShiptConstant as SC;
use Override;

/**
 * CSVファイルから取得した商品情報1件分のクラス
 *
 * @since 6.1.0
 */
class CsvOrderItemResource extends OrderItemResource {

    #[Override]
    protected function id():int
    {
        return -1;
    }

    #[Override]
    protected function shipment():int
    {
        return $this[SC::SHIPMENT];
    }


}
