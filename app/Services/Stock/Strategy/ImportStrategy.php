<?php
namespace App\Services\Stock\Strategy;
use App\Services\Stock\StockpileRow;

interface ImportStrategy {

    public function getSetCode(StockpileRow $row);


}