<?php
namespace App\Files\Csv\Tax;
use App\Files\Csv\CsvWriter;

class TaxCsvWriter {

    public function write(array $data) {
        $filename = "taxreturn.csv";
        $writer = new CsvWriter();
        $csvdata = $this->toCsvData($data);
        $writer->write($filename, [], $csvdata);
    }

    /**
     * Notionの情報をCSVファイルのデータ行に変換する。
     *
     * @return array
     */
    private function toCsvData($data) {
        $list = [];
        foreach($data as $d) {
            $row = [$d->getShippingDate()->format('Y/m/d')];
            // 日付
            $list[] = $row;
        }
        return $list;
    }
}