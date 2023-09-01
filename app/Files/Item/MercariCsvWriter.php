<?php
namespace App\Files\Item;

use App\Files\Csv\ItemCsvWriter;
use App\Models\CardInfo;

/**
 * Mercari用商品CSV作成クラス
 */
class MercariCsvWriter extends ItemCsvWriter {

    protected function toCsv(CardInfo $row) {
        return [];
    }

    protected function shopname() {
        return 'mercari';
    }

}