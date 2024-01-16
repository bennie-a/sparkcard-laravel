<?php
namespace App\Files\Csv\Tax;
use App\Files\Csv\CsvWriter;
use DateTime;

class TaxCsvWriter {

    public function write(array $data) {
        $now = new DateTime();
        $filename = sprintf("taxreturn_%s.csv", $now->format('Ymd'));
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
            $attr = $d->getExpansion()['attr'];
            $name = $d->getName();
            $description = sprintf("【%s】%s[%s](%s)", $attr, $d->getName(), $d->getLangAbbr(), $d->getOrderNo());
            $row = [$d->getShippingDate()->format('Y/m/d'), $d->getPrice() * $d->getStock(), $description];

            // 日付
            $list[] = $row;
        }
        return $list;
    }
}