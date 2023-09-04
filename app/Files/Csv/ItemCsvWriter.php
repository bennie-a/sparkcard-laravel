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
            fputcsv($f, $header);

            // data
            foreach($data as $row) {
                fputcsv($f, $this->toCsv($row));
            }
        }
        fclose($f);
    }
    
    protected abstract function toCsv(CardInfo $row);
    
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


}