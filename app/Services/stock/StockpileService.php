<?php
namespace App\Services\stock;

use App\Files\CsvReader;

/**
 * 在庫管理機能のサービスクラス
 */
class StockpileService {

    /**
     * CSVファイルの内容をDBに登録する。
     *
     * @param string $path ファイルパス
     * @return void
     */
    public function import(string $path) {
        logger()->info('在庫管理ファイル読み込み開始', [$path]);

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