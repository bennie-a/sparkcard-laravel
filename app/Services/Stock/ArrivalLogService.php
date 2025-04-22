<?php
namespace App\Services\Stock;

use App\Libs\CarbonFormatUtil;
use App\Libs\MtgJsonUtil;
use App\Models\Stockpile;
use app\Services\Stock\ArrivalParams;
use App\Models\ArrivalLog;
use App\Models\CardInfo;
use App\Services\Constant\StockpileHeader as Header;
use App\Services\Constant\ArrivalConstant as Con;
use App\Services\Constant\CardConstant;
use App\Services\Constant\SearchConstant;
use App\Services\Constant\GlobalConstant as GCon;

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
        $logs = ArrivalLog::filtering($details);
        if ($logs->isEmpty()) {
            return $logs;
        }
        $first = $logs->first();
        
        $data = [
            Con::VENDOR => [GCon::ID => $first->vendor_type_id,
            CardConstant::NAME => $first->vcat,
            Con::SUPPLIER => $first->vendor
            ]
        ];
        if (MtgJsonUtil::hasKey(Con::ARRIVAL_DATE, $details)) {
            $data[Con::ARRIVAL_DATE] =  CarbonFormatUtil::toDateString($details[Con::ARRIVAL_DATE]);
        }
        $results = collect([GCon::DATA => $data, GCon::LOGS => $logs]);
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
        $arrivalLog = ArrivalLog::create(['stock_id' => $stockpile->id, Con::ARRIVAL_DATE => $params->arrivalDate(), 
                                        SearchConstant::VENDOR_TYPE_ID => $params->vendorType(), Con::VENDOR => $params->vendor(), 
                                        Header::QUANTITY => $params->quantity(), Header::COST => $params->cost()]);
        return $arrivalLog;
    }

    /**
     * 入荷IDと合致する入荷情報を在庫情報及び出荷情報と
     * 一緒に取得する。
     *
     * @param integer $id
     * @return Model
     */
    public function findByStockInfo(int $id) {
        $log = ArrivalLog::findWithStockInfo($id);
        return $log;
    }
}