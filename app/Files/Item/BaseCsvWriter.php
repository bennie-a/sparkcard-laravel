<?php
namespace App\Files\Item;

use App\Files\Csv\ItemCsvWriter;
use App\Models\CardInfo;

/**
 * BASE用商品CSV作成クラス
 */
class BaseCsvWriter extends ItemCsvWriter {

    protected function toCsv(CardInfo $row) {
        return [];
    }

    protected function shopname() {
        return 'base';
    }

}