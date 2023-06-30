<?php

/**
 * 在庫情報CSV1件分のクラス
 */
class StockpileRow {
    
    public function __construct(array $row)
    {
        $this->row = $row;
    }

    public function setcode() {
        return $this->row[StockpileHeader::SETCODE];
    }
}