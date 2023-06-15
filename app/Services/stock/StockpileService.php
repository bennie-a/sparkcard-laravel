<?php
namespace App\Services\Stock;

use App\Files\CsvReader;
use App\Files\Stock\StockpileCsvReader;

/**
 * 在庫管理機能のサービスクラス
 */
class StockpileService extends AbstractSmsService{

    /**
     * 出荷ログ用のCSV読み込みクラスを取得する。
     * @see CsvReader::csvReader
     * @return StockpileCsvReader
     */
    protected function csvReader() {
        return new StockpileCsvReader();
    }

}