<?php
namespace App\Services\Constant;

class StockpileHeader {
    public const SETCODE = 'setcode';
    const NAME = 'name';
    const LANG = 'lang';
    const CONDITION = 'condition';
    const QUANTITY = 'quantity';
    const IS_FOIL = 'isFoil';
    const EN_NAME = 'en_name';

    /**
     * 定数一覧を取得する。
     *
     * @return array
     */
    public static function constants() {
        return [StockpileHeader::SETCODE,StockpileHeader::NAME,
        StockpileHeader::LANG,StockpileHeader::CONDITION,
        StockpileHeader::QUANTITY,StockpileHeader::IS_FOIL,
        StockpileHeader::EN_NAME];
    }
}