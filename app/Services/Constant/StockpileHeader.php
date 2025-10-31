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

    // 出荷CSV(メルカリ)
    public const ORDER_ID = "order_id";
    public const BUYER = 'buyer_name';
    public const PRODUCT_NAME = "product_name";
    public const PRODUCT_PRICE = "product_price";
    public const SHIPPING_DATE = "shipping_date";
    public const PRODUCT_ID = "original_product_id";
    public const BILLING_POSTAL_CODE = "billing_postal_code";
    public const BILLING_STATE = "billing_state";
    public const BILLING_CITY = "billing_city";
    public const BILLING_ADDRESS_1 = "billing_address_1";
    public const BILLING_ADDRESS_2  = "billing_address_2";
    
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

    public static function shippinglog_constants() {
        return [self::ORDER_ID, self::SHIPPING_DATE, self::PRODUCT_ID, self::BUYER, self::PRODUCT_NAME, self::QUANTITY,
                     self::PRODUCT_PRICE, self::BILLING_POSTAL_CODE, self::BILLING_STATE, self::BILLING_CITY,
                    self::BILLING_ADDRESS_1,self::BILLING_ADDRESS_2];
    }
}