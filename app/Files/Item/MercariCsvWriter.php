<?php
namespace App\Files\Item;

use App\Files\Csv\ItemCsvWriter;
use App\Models\CardInfo;

/**
 * Mercari用商品CSV作成クラス
 */
class MercariCsvWriter extends ItemCsvWriter {

    protected function toCsv(CardInfo $row) {
        return [$this->thumbnail($row), $this->itemImage($row), $this->itemname($row),
                 $this->description(), '', '0', $row->price, 'iV9pczaBytZwZQGxHf6gqN', '1', '1', 'jp27', '1', '2'];
    }

    public function shopname() {
        return 'mercari';
    }

}