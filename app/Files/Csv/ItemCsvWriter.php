<?php
namespace App\Files\Csv;

use App\Enum\CardColor;
use App\Models\CardInfo;
use App\Models\CsvHeader;

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
    public function write(string $set, string $color, $data) {
        $dir = env('CSV_EXPORT');
        $filename = sprintf('%s_item_%s_%s.csv', $this->shopname(), $set, $color);
        $f = fopen($dir.$filename, 'w');
        if ($f) {
            // header
            $header = CsvHeader::findColumns($this->shopname());
            fputcsv($f, $header);

            // data
            foreach($data as $row) {
                $price = $this->roundPrice($row->price);
                // 一定金額以下は除外
                if ($price < $this->basevalue()) {
                    continue;
                }
                fputcsv($f, $this->toCsv($price, $row));
            }
        }
        fclose($f);
    }
    
    protected abstract function toCsv(int $price, CardInfo $row);
    
    /**
     * 除外する金額を取得する。
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
在庫：0点

■状態
状態は【NM】です。ドラフトブースターパックから出ました。開封直後にスリーブに入れて保管しています。';
        return sprintf($desc, $row->name, $foil, $row->exp_name, $row->exp_attr);
    }
    
    protected function thumbnail(CardInfo $row) {
        $enname = $row->en_name;
        return sprintf('%s_%s-min.jpg', $enname, $this->shopname());
    }

    protected function itemImage(CardInfo $row) {
        $enname = $row->en_name;
        return sprintf('%s-min.jpg', $enname);
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
}