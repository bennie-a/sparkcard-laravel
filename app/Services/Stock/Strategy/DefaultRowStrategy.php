<?php
namespace App\Services\Stock\Strategy;

use App\Services\Stock\StockpileRow;

/**
 * @deprecated 5.1.0
 */
class DefaultRowStrategy implements ImportStrategy {

    public function getSetCode(StockpileRow $row) {
        return $row->setcode();
    }

}