<?php
namespace App\Services\Stock\Strategy;
use App\Services\Stock\StockpileRow;

/**
 * Undocumented interface
 */
interface ImportStrategy {

    public function getSetCode(StockpileRow $row);


}