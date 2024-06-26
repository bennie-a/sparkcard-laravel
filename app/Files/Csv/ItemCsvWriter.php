<?php
namespace App\Files\Csv;

use App\Enum\CardColor;
use App\Models\CardInfo;
use App\Models\CsvHeader;
use App\Models\Promotype;

/** 
 * 商品登録用CSVファイルを作成するクラス
 */
abstract class ItemCsvWriter {

    /**
     * 商品登録用CSVファイルを作成する。
     * @param string $set
     * @param string $color
     * @param $data
     * @return void
     */
    public function write(string $set, string $color, int $number, $data) {
        $filename = sprintf('%s_item_%s_%s.csv', $this->shopname(), $set, $color);

        // header
        $header = CsvHeader::findColumns($this->shopname());

        // data
        $csvdata = [];
        foreach($data as $row) {
            $price = $this->roundPrice($row->price);
            $separator = ' // ';
            if (str_contains($row->en_name, $separator)) {
                $row->en_name = explode($separator, $row->en_name)[0];
            }
            // $promotype = $this->extractPromotype($row->name);
            // $row->promotype = $promotype;
            // 一定金額以下は除外
            if ($price < $this->basevalue()) {
                continue;
            }
            $row = $this->toCsv($price, $number, $row);
            $csvdata[] = $row;
            $number++;
        }

        $writer = new CsvWriter();
        $writer->write($filename, $header, $csvdata);
    }
    
    /**
     * CSVファイルに出力するヘッダーを取得する。
     *
     * @return void
     */
    protected function getHeader() {
        return CsvHeader::findColumns($this->shopname());
    }

    protected abstract function toCsv(int $price, int $number, CardInfo $row);
    /**
     * 除外する下限金額を取得する。
     *ここで取得した金額以下の商品は除外する。
     * @return int
     */
    protected abstract function basevalue():int;
    public abstract function shopname();
    
    protected function description(CardInfo $row) {
        $foil = $row->isFoil ? "[Foil]" : "";
        $desc = '■商品内容
商品名：「%s%s」
エキスパンション：%s(%s)
言語：日本語

■状態
状態は【%s】です。ドラフトブースターパックから出ました。開封直後にスリーブに入れて保管しています。';
        return sprintf($desc, $row->name, $foil, $row->exp_name, 
                    $row->exp_attr, $row->condition, $row->quantity);
    }
    
    protected function thumbnail(CardInfo $row) {
        return sprintf('%s_%s_thumb.jpg', $row->number, $row->exp_attr);
    }

    protected function itemImage(CardInfo $row) {
        return sprintf('%s_%s.jpg', $row->number, $row->exp_attr);
    }

    /**
     * 商品名を取得する。
     *
     * @param CardInfo $row
     * @return string 
     */
    protected function itemname(CardInfo $row) {
        $attr = $row->exp_attr;
        $color = CardColor::tryFrom($row->color_id);
        $format = '【%s】%s[JP][%s]';
        if ($row->isFoil) {
            $format = '【%s】【Foil】%s[JP][%s]';
        }
        return sprintf($format, $attr, $row->name, $color->text());
    }

    /**
     * 金額を1の位で四捨五入する。
     *
     * @param string $inputAmount
     * @return int
     */
    protected function roundPrice(string $inputAmount) {
        $amountWithoutComma = (int)str_replace(',', '', $inputAmount);
        $roundedAmount = round($amountWithoutComma/10, 0) * 10;
        return $roundedAmount;
    }

    protected function concatPromotype(string $enname, string $promotype) {
        if (!empty($promotype)) {
            $enname .= '_'.$promotype;
        }
        return $enname;
    }

    private function extractPromotype(string $name) {
        $pattern = '/≪([^≫]+)≫/';

        if (preg_match($pattern, $name, $matches)) {
            $extractedText = $matches[1];
            $promotype = Promotype::findCardByName($extractedText);
            return $promotype->attr;
        } 
        return '';
    }
}