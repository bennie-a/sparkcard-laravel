<?php
namespace App\Files\Item;

use App\Files\Csv\ItemCsvWriter;
use App\Models\CardInfo;

/**
 * BASE用商品CSV作成クラス
 */
class BaseCsvWriter extends ItemCsvWriter {

    protected function toCsv(CardInfo $row) {
        return ['', $this->itemname($row), '', '', $this->description(), 
                        $row->price, '1', '0', '0', $row->number, 
                        '', $this->thumbnail($row), $this->itemImage($row)];
    }

    public function shopname() {
        return 'base';
    }


}