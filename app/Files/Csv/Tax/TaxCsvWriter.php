<?php
namespace App\Files\Csv\Tax;
use App\Files\Csv\CsvWriter;
use DateTime;

class TaxCsvWriter {

    /**
     * 青色申告用のCSVファイルをShift-JISで出力する。
     *
     * @param array $data
     * @return void
     */
    public function write(array $data) {
        $now = new DateTime();
        $filename = sprintf("taxreturn_%s.csv", $now->format('Ymd'));
        $writer = new CsvWriter();
        $csvdata = $this->toCsvData($data);
        $writer->write($filename, [], $csvdata, 'convert.iconv.utf-8/cp932');
    }

    /**
     * Notionの情報をCSVファイルのデータ行に変換する。
     *
     * @return array
     */
    private function toCsvData($data) {
        $list = [];
        foreach($data as $d) {
            $attr = $d->getExpansion()['attr'];
            $description = sprintf("【%s】%s[%s]", $attr, $d->getName(), $d->getLangAbbr());
            $row = [$d->getShippingDate()->format('Y/m/d'), $d->getPrice() * $d->getStock(), '', $description];

            // 日付
            $list[] = $row;
        }
        return $list;
    }
}