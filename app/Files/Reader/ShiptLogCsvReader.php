<?php
namespace App\Files\Reader;

use App\Enum\CsvFlowType;
use App\Enum\ShopPlatform;
use App\Files\Csv\CsvReader;
use App\Http\Validator\ShiptValidator;
use App\Models\CsvHeader;
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
        $headers = CsvHeader::findColumns(
            ShopPlatform::MERCARI,
            CsvFlowType::SHIPT
        );

        return $headers;
    }

    protected function validator()
    {
        return new ShiptValidator();
    }

}