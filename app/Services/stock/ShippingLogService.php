<?php
namespace App\Services\Stock;

use App\Files\CsvReader;
use App\Files\Stock\ShippingLogCsvReader;

/**
 * 出荷ログ機能のサービスクラス
 */
class ShippingLogService extends AbstractSmsService{

    /**
     * 出荷ログ用のCSV読み込みクラスを取得する。
     * @see CsvReader::csvReader
     * @return ShippingLogCsvReader
     */
    protected function csvReader() {
        return new ShippingLogCsvReader();
    }

        /**
     * @see AbstractSmsService::store
     * @param StockpileRow $row
     * @return void
     */
    protected function store($row) {
        logger()->info([$row->card_name(), $row->setcode(), $row->language(), $row->isFoil()]);
    }

    protected function createRow(int $index, array $row) {
        return new ShippingRow($index, $row);
    }


}