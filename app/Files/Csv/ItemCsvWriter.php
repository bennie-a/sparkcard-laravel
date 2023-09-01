<?php
namespace App\Files\Csv;

use App\Models\CardInfo;
use App\Models\CsvHeader;

/** 
 * 商品登録用CSVファイルを作成するクラス
 */
abstract class ItemCsvWriter {

    /**
     * 商品登録用CSVファイルを作成する。
     *
     * @param string $shop
     * @param $data
     * @return void
     */
    public function write($data) {
        $dir = 'C:\Users\salto\OneDrive\ドキュメント\beninekoya_doc\woe\\';
        $filename = $this->shopname().'_item.csv';
        $f = fopen($dir.$filename, 'w');
        if ($f) {
            // header
            $header = CsvHeader::findColumns($this->shopname());
            // logger()->debug('Columns', $header);
            fputcsv($f, $header);

            // data
            // fputcsv($f, ['あいこ', '28']);
        }
        fclose($f);
    }

    protected abstract function toCsv(CardInfo $row);

    protected abstract function shopname();
}