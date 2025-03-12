<?php
namespace App\Services\Stock;

use App\Models\Stockpile;
use app\Services\Stock\ArrivalParams;
use App\Models\ArrivalLog;
use App\Models\CardInfo;
use App\Services\Constant\StockpileHeader as Header;

/**
 * 入荷手続きに関するServiceクラス
 */
class ArrivalLogService {

    /**
     * カード情報を含む入荷情報を取得する。
     * @return Collection
     */
    public function fetch($details) {
        $results = ArrivalLog::fetch($details);
        return $results;
    }

    public function filtering($details) {
        $results = ArrivalLog::filtering($details);
        return $results;
    }

    /**
     * 入荷ログを1件登録する。
     *
     * @param array $details
     * @return CardInfo
     */
    public function store(ArrivalParams $params) {

        $stockpile = Stockpile::findSpecificCard($params->cardId(), $params->language(), $params->condition());
        if (empty($stockpile->id)) {
            // 在庫情報なし
            $stockpile = Stockpile::create([Header::CARD_ID => $params->cardId(), Header::LANGUAGE => $params->language(),
            Header::CONDITION => $params->condition(), Header::QUANTITY => $params->quantity()]);
        } else {
            //在庫情報あり
            $stockpile->quantity += $params->quantity();
            $stockpile->update();
        }
        $arrivalLog = ArrivalLog::create(['stock_id' => $stockpile->id, Header::ARRIVAL_DATE => $params->arrivalDate(), 
                                        Header::VENDOR_TYPE_ID => $params->vendorType(), Header::VENDOR => $params->vendor(), 
                                        Header::QUANTITY => $params->quantity(), Header::COST => $params->cost()]);
        return $arrivalLog;
    }
}