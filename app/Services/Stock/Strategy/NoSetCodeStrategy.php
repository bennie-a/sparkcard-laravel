<?php
namespace App\Services\Stock\Strategy;

use App\Exceptions\ConflictException;
use App\Services\Stock\StockpileRow;

/**
 * @deprecated 5.1.0
 */
class NoSetCodeStrategy implements ImportStrategy {

    public function getSetCode(StockpileRow $row) {
        $info = \App\Facades\ScryfallServ::getCardInfoByName($row->setcode(), $row->en_name());
        if ($info['reprint'] === true) {
            throw new ConflictException($row->setcode());
        }
        return strtoupper($info['setcode']);
    }
}