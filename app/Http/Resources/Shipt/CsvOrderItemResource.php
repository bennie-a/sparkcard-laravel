<?php

namespace App\Http\Resources\Shipt;

use App\Http\Resources\Shipt\OrderItemResource;
use App\Models\Stockpile;
use App\Services\Constant\ShiptConstant as SC;
use App\Services\Constant\StockpileHeader;
use Illuminate\Http\Request;
use Override;

/**
 * CSVファイルから取得した商品情報1件分のクラス
 *
 * @since 6.1.0
 */
class CsvOrderItemResource extends OrderItemResource {

    #[Override]
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);
        $data[SC::IS_REGISTERED] = $this[SC::IS_REGISTERED];
        return $data;
    }
    #[Override]
    protected function id():int
    {
        return -1;
    }

    #[Override]
    protected function shipment():int
    {
        if ($this[SC::IS_SET]) {
            return $this[SC::QUANTITY] * $this[SC::PCS_PER_SET];
        }
        return $this[SC::QUANTITY];
    }

    #[Override]
    protected function subtotal():int
    {
        return $this[SC::ITEM_SUBTOTAL];
    }

    #[Override]
    protected function unitPrice():int
    {
        return $this[SC::UNIT_PRICE];
    }

    #[Override]
    protected function stockpile():Stockpile
    {
        return $this[StockpileHeader::STOCK];
    }
}
