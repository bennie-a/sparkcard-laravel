<?php
namespace App\Services\Constant;

use App\Services\Constant\StockpileHeader;

/**
 * 出荷情報の定数クラス
 */
class ShiptConstant extends StockpileHeader {
    // 出荷CSV(メルカリ)
    public const ORDER_ID = "order_id";
    public const BUYER = 'buyer_name';
    public const PRODUCT_NAME = "product_name";
    public const PRODUCT_PRICE = "product_price";
    public const SHIPPING_DATE = "shipping_date";
    public const PRODUCT_ID = "original_product_id";
    public const POSTAL_CODE = "billing_postal_code";
    public const STATE = "billing_state";
    public const CITY = "billing_city";
    public const ADDRESS_1 = "billing_address_1";
    public const ADDRESS_2  = "billing_address_2";
    public const ZIPCODE = "zipcode";
    public const ADDRESS = "address";

    public static function shippinglog_constants() {
        return [self::ORDER_ID, self::SHIPPING_DATE, self::PRODUCT_ID, self::BUYER, self::PRODUCT_NAME, self::QUANTITY,
                     self::PRODUCT_PRICE, self::POSTAL_CODE, self::STATE, self::CITY, self::ADDRESS_1,self::ADDRESS_2];
    }
}