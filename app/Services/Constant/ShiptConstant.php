<?php
namespace App\Services\Constant;

use App\Services\Constant\StockpileHeader;
use Deprecated;

/**
 * 出荷情報の定数クラス
 */
class ShiptConstant extends StockpileHeader {
    // 出荷CSV(メルカリ)
    public const ORDER_ID = "order_id";
    public const BUYER = 'buyer_name';
    public const PRODUCT_NAME = "product_name";
    public const PRODUCT_PRICE = "product_price";
    public const SHIPT_DATE = "shipt_date";
    public const PRODUCT_ID = "original_product_id";
    public const POSTAL_CODE = "shipping_postal_code";
    public const STATE = "shipping_state";
    public const CITY = "shipping_city";
    public const ADDRESS_1 = "shipping_address_1";
    public const ADDRESS_2  = "shipping_address_2";
    public const ZIPCODE = "zip_code";
    public const ADDRESS = "address";
    public const DISCOUNT_AMOUNT = "coupon_discount_amount";
    public const FEE = "shipt_fee";
    public const ITEMS = "items";
    public const SHIPMENT = 'shipment';

    /** @deprecated 6.1.0 */
    public const SINGLE_PRICE = 'single_price';

    public const TOTAL_PRICE = 'total_price';
    public const BUYER_INFO = 'buyer_info';
    public const STOCK_ID = 'stock_id';
    public const IS_REGISTERED = 'isRegistered';
    public const ITEM_COUNT = 'item_count';
    public const ITEM_SUBTOTAL = 'items_subtotal';
    public const GRAND_TOTAL = 'grand_total';
    public const UNIT_PRICE = 'unit_price';
    public const SUBTOTAL = 'subtotal';
    public const PLATFORM = 'platform';
    public const PLATFORM_ORDER_ID = 'platform_order_id';
    public const PREV_ID = 'prev_id';
    public const NEXT_ID = 'next_id';
    public const PRICE = 'price';
    public const FEE_ID = 'shipt_fee_id';
    public const METHOD = 'method';
}
