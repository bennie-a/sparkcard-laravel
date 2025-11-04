<?php
namespace App\Files\Reader;

use App\Files\Csv\CsvReader;
use App\Http\Validator\ShippingValidator;
use App\Services\Constant\ShiptConstant;
/**
 * メルカリ用注文CSVの読み込みクラス
 */
class ShiptLogCsvReader extends CsvReader {
    /**
     * 出荷ログファイルのヘッダーを取得する。
     * @see CsvReader::csvHeaders
     * @return array
     */
    protected function csvHeaders() {
        return ShiptConstant::shippinglog_constants();
    }

    protected function validator()
    {
        return new ShippingValidator();
    }

}