<?php
namespace App\Files\Stock;

use App\Files\Csv\CsvReader;
use App\Http\Validator\ShippingValidator;
use App\Services\Constant\StockpileHeader as Header;
class ShippingLogCsvReader extends CsvReader {
    /**
     * 出荷ログファイルのヘッダーを取得する。
     * @see CsvReader::csvHeaders
     * @return array
     */
    protected function csvHeaders() {
        return Header::shippinglog_constants();
    }

    protected function validator()
    {
        return new ShippingValidator();
    }

}