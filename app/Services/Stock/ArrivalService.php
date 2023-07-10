<?php
namespace App\Services\Stock;

/**
 * 入荷手続きに関するServiceクラス
 */
class ArrivalService {

    /**
     * 入荷ログを1件登録する。
     *
     * @param array $details
     * @return void
     */
    public function store(array $details) {
        \CardBoard::store($details);
    }

}