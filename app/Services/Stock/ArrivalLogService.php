<?php
namespace App\Services\Stock;

use App\Facades\CardBoard;
use App\Facades\StockpileServ;
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
use App\Services\Constant\GlobalConstant;
use Illuminate\Database\Eloquent\Model;

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
     * @return ArrivalLog
     */
    public function findByStockInfo(int $id) {
        $log = ArrivalLog::findWithStockInfo($id);
        return $log;
    }

    public function findById(int $id) {
        $log = ArrivalLog::find($id);
        return $log;
    }

    public function update(int $id, array $details) {
        if (MtgJsonUtil::hasKey(Header::QUANTITY, $details)) {
            $log = ArrivalLog::findWithStockInfo($id);
            $stockServ = new StockpileService();
            $stockServ->updateQty($log->stock_id, $log->quantity, $details[Header::QUANTITY]);
        }

        $log = ArrivalLog::where(GlobalConstant::ID, $id)->first();
        $log->fill($details)->update();
        $result = $this->findById($id);
        return $result;
    }

    /**
     * 在庫情報の枚数を入荷情報の枚数分減らし、入荷情報を削除する。
     *
     * @param Model $log
     * @return void
     */
    public function destroy($log) {
        $stockserv = new StockpileService();
        ArrivalLog::destroy($log->id);
        $stockserv->decreaseQuantity($log->stock_id, $log->quantity);
    }
}