<?php
namespace App\Files\Item;

use App\Files\Csv\ItemCsvWriter;
use App\Models\CardInfo;

/**
 * BASE用商品CSV作成クラス
 */
class BaseCsvWriter extends ItemCsvWriter {

    protected function toCsv(int $price, int $number, CardInfo $row) {
        return ['', $this->itemname($row), '', '', $this->description($row), 
                        $this->minusShipping($price), '1', $row->quantity, '1', $number, 
                        '', $this->thumbnail($row), $this->itemImage($row)];
    }

    public function shopname() {
        return 'base';
    }

    /**
     * @see ItemCsvWriter basevalue()
     *
     * @return integer
     */
    protected function basevalue():int {
        return 50;
    }


    /**
     * 送料抜き価格を計算する。
     *
     * @param integer $price
     * @return int
     */
    private function minusShipping(int $price) {
        if ($price < 300) {
            return $price;
        }
        if ($price >= 300 && $price < 1500) {
            return $price - 80;
        }
        return $price -180;
    }
}