<?php
namespace App\Files\Item;

use App\Files\Csv\ItemCsvWriter;
use App\Models\CardInfo;

/**
 * Mercari用商品CSV作成クラス
 */
class MercariCsvWriter extends ItemCsvWriter {

    protected function toCsv(int $price, int $number, CardInfo $row) {
        $row->price = $price;
        if ($price < 300) {
            $quantity = $this->calcQuantity($price);
            $price = $price * $quantity;
        }

        $lines =  [$this->thumbnail($row), $this->itemImage($row)];
        $empty = array_fill(0, 8, '');
        $lines = array_merge($lines, $empty);
        array_push($lines, $this->itemname($row), $this->description($row), '', $row->quantity);
        $emptyItems = array_fill(count($lines), 39, '');
        $lines = array_merge($lines, $emptyItems);
        array_push($lines, $price, 'iV9pczaBytZwZQGxHf6gqN', '1', '1', 'jp27', '1', '2');
        return $lines;
    }

    /**
     * 商品名を取得する。
     * @see ItemCsvWriter#itemname
     * @param CardInfo $row
     * @return string 
     */
    protected function itemname(CardInfo $row) {
        if ((int)$row->price < 300) {
            $quantity = $this->calcQuantity($row->price);
            if ($quantity > 1) {
                $row->name .= sprintf('%s枚セット', $quantity);
            }
        }
        return parent::itemname($row);
    }

    public function shopname() {
        return 'mercari';
    }

    protected function description(CardInfo $row) {
        $contents = parent::description($row);
        $before = '≪2品以上のお買い上げで割引します♪希望の方はお問い合わせ欄にて承ります(o^^o)≫';
        $after = '■発送について
スリーブに入れた商品をおまけのカードと一緒に透明袋に梱包して【%s】で発送します。
おまけカードは基本土地カードに変更OKです。購入後の取引メッセージでご希望の色をお伝えください。

≪セット割実施中!!≫
2品以上購入したいなら割引のチャンス!!方法はお問い合わせ欄に欲しい商品名と枚数を送るだけ。
後はこちらで用意した専用商品欄から購入すればOK♪

■割引一覧
2品⇒5%%引き 3品⇒10%%引き 4品以上⇒15%%引き

#ベニネコヤ #TCG #トレーディングカード #mtg #MTGシングル #MTGカード トレカ マジック・ザ・ギャザリング スタンダード';
        return $before."\n".$contents."\n".sprintf($after, $this->getDelivery($row->price));
    }

    private function getDelivery($price) {
        if ($price >= 10000) {
            return "簡易書留";
        } else if ($price >= 1500 && $price < 10000) {
            return "クリックポスト";
        }
        return "ミニレター";
    }

    /**
     * @see ItemCsvWriter basevalue()
     *
     * @return integer
     */
    protected function basevalue():int {
        return 80;
    }

    protected function combinedPrice(string $price) {
        $quantity = $this->calcQuantity($price);
        return $price * $quantity;
    }


    /**
     * セットで販売する枚数を計算する。
     *
     * @param integer $price
     * @return int セット枚数
     */
    private function calcQuantity(string $price) {
        $intPrice = (int)$price;
        switch($intPrice) {
            case $intPrice >= 80 && $intPrice < 95:
                return 4;
            case $intPrice >= 95 && $intPrice < 150:
                return 3;
            case $intPrice > 150 && $intPrice <= 299:
                return 2; 
            default:
            return 1;
        }
    }
}