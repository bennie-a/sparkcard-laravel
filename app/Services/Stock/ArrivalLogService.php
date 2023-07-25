<?php
namespace App\Services\Stock;

use App\Models\Stockpile;
use app\Services\Stock\ArrivalParams;
use App\Facades\StockpileServ;
use App\Models\ArrivalLog;

/**
 * 入荷手続きに関するServiceクラス
 */
class ArrivalLogService {

    /**
     * 入荷ログを1件登録する。
     *
     * @param array $details
     * @return void
     */
    public function store(ArrivalParams $params) {
        $stockpile = Stockpile::findSpecificCard($params->cardId(), $params->language(), $params->condition());
        if (empty($stockpile)) {
            // 在庫情報なし
            $stockpile = \StockpileServ::create($params->cardId(), $params->language(), $params->condition(), $params->quantity());
        } else {
            //在庫情報あり
            $stockpile->quantity += $params->quantity();
            $stockpile->update();
        }
        ArrivalLog::create(['stock_id' => $stockpile->id, 'arrival_date' => $params->arrivalDate(), 
                                        'supplier' => $params->supplier(), 'quantity' => $params->quantity(), 'cost' => $params->cost()]);
        // \CardBoard::store($details);
    }
}