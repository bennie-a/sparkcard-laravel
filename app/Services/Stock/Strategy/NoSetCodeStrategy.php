<?php
namespace App\Services\Stock\Strategy;

use App\Services\Stock\StockpileRow;

class NoSetCodeStrategy implements ImportStrategy {

    public function getSetCode(StockpileRow $row) {
        $info = \App\Facades\ScryfallServ::getCardInfoByName($row->setcode(), $row->en_name());
        if ($info['reprint'] === true) {
            throw new ConflictException('収録セットが複数あります');
        }
        return strtoupper($info['setcode']);
    }

}