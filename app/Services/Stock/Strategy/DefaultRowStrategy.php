<?php
namespace App\Services\Stock\Strategy;

use App\Services\Stock\StockpileRow;

class DefaultRowStrategy implements ImportStrategy {

    public function getSetCode(StockpileRow $row) {
        return $row->setcode();
    }

}