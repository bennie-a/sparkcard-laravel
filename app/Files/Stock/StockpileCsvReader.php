<?php
namespace App\Files\Stock;

use App\Files\CsvReader;

class StockpileCsvReader extends CsvReader {
        /** 
     * 在庫管理ファイルのヘッダーを指定する。
     * @see CsvReader::csvHeaders
     * @return array
     */
    protected function csvHeaders() {
        return ['setcode','name','lang','condition','quantity', 'isFoil'];
    }

}