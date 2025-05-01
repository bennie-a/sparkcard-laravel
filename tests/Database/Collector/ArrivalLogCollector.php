<?php

namespace Tests\Database\Collector;

use App\Models\ArrivalLog;
/**
 * テスト用の入荷情報抽出クラス
 */
class ArrivalLogCollector
{
    /**
     * 入荷情報のIDからカード情報を取得する。
     *
     * @param integer $id
     * @return ArrivalLog
     */
    public function getCardInfoFromArrivalId(int $id) {
        $log = ArrivalLog::join('stockpile as s', 'arrival_log.stock_id', '=', 's.id')
                                        ->join('card_info as c', 's.card_id', '=', 'c.id')
                                        ->join('expansion as e', 'e.notion_id', '=', 'c.exp_id')
                                        ->join('foiltype as f', 'f.id', '=', 'c.foiltype_id')
                                        ->where('arrival_log.id', $id)
                                        ->select('arrival_log.id', 'arrival_log.stock_id', 'e.attr as attr', 'c.name as cardname', 'arrival_log.quantity',
                                                                                         's.language as lang', 's.card_id', 'c.isFoil as is_foil', 'f.name as foiltag')
                                        ->first();
        return $log;
    }

    /**
     * カード名を元に入荷情報を取得する。
     *
     * @param string $cardname
     * @return void
     */
    public function fetchByCardname(string $cardname) {
        return $this->getQuery()->where('c.name', $cardname)->first();
    }

    private function getQuery() {
        $query = ArrivalLog::join('stockpile as s', 'arrival_log.stock_id', '=', 's.id')
                                        ->join('card_info as c', 's.card_id', '=', 'c.id')
                                        ->join('expansion as e', 'e.notion_id', '=', 'c.exp_id')
                                        ->join('foiltype as f', 'f.id', '=', 'c.foiltype_id')
                                        ->select('arrival_log.id', 'arrival_log.stock_id', 'e.attr as attr', 'c.name as cardname', 's.quantity as qty',
                                                        'arrival_log.quantity as rog', 's.language as lang', 's.card_id', 'c.isFoil as is_foil', 'f.name as foiltag');

        return $query;
    }
}