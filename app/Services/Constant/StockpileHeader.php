<?php
namespace App\Services\Constant;

class StockpileHeader {
    public const SETCODE = 'setcode';
    const SETNAME = 'setname';
    const NAME = 'name';
    const LANG = 'lang';
    const CONDITION = 'condition';
    const QUANTITY = 'quantity';
    const IS_FOIL = 'isFoil';
    const EN_NAME = 'en_name';
    const COST = 'cost';
    const MARKET_PRICE = 'market_price';

    const CARD_ID = 'card_id';
    const LANGUAGE = 'language';
    const FOIL = 'foil';
    const STOCK = 'stock';

    /**
     * 定数一覧を取得する。
     *
     * @return array
     */
    public static function stockpile_constants() {
        return [StockpileHeader::SETCODE,StockpileHeader::NAME,
        StockpileHeader::LANG,StockpileHeader::CONDITION,
        StockpileHeader::QUANTITY,StockpileHeader::IS_FOIL,
        StockpileHeader::EN_NAME];
    }
}