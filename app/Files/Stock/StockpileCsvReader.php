<?php
namespace App\Files\Stock;

use App\Files\CsvReader;
use App\Http\Validator\StockpileValidator;

class StockpileCsvReader extends CsvReader {
        /** 
     * 在庫管理ファイルのヘッダーを指定する。
     * @see CsvReader::csvHeaders
     * @return array
     */
    protected function csvHeaders() {
        return ['setcode','name','lang','condition','quantity', 'isFoil', 'en_name'];
    }

    protected function validator()
    {
        return new StockpileValidator();
    }
}