<?php
namespace App\Files\Stock;

use App\Files\CsvReader;

class ShippingLogCsvReader extends CsvReader {
    /**
     * 出荷ログファイルのヘッダーを取得する。
     * @see CsvReader::csvHeaders
     * @return array
     */
    protected function csvHeaders() {
        return ["name", "zipcode", "address", "setcode", "item_name", "quantity", "single_price", "lang", "shipping_date"];
    }

}