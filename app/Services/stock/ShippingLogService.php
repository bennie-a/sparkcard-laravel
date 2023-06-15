<?php
namespace App\Services\stock;

use App\Files\CsvReader;

/**
 * 出荷ログ機能のサービスクラス
 */
class ShippingLogService {

    /**
     * CSVファイルの内容をDBに登録する。
     *
     * @param string $path ファイルパス
     * @return void
     */
    public function import(string $path) {
        logger()->info('出荷ログファイル読み込み開始', [$path]);

        // $reader = new CsvReader($this->csvHeaders());
        // $records = $reader->read($path);
    }

    /** 
     * CSVファイルのヘッダーを指定する。
     */
    protected function csvHeaders() {
        return ['setcode','name','lang','condition','quantity'];
    }
}