<?php
namespace App\Services\Shipt;

use App\Enum\ShopPlatform;
use App\Services\Constant\GlobalConstant;
use App\Services\Shipt\ShiptRow;
use App\Services\Constant\ShiptConstant as SC;

class ShiptStoreRow extends ShiptRow
{

    public function __construct(array $info)
    {
        return parent::__construct(0, $info);
    }

    /**
     * 行番号を取得する。
     * ※使用しないため-1を返す。
     * @return int
     */
    public function number():int {
        return -1;
    }

    public function platform():ShopPlatform {
        return $this->row[SC::PLATFORM];
    }

    public function postal_code() {
        return $this->row[SC::ZIPCODE];
    }

    public function address() {
        return $this->row[SC::ADDRESS];
    }

    /**
     * 1枚あたりの単価を計算する。
     *
     * @return int
     */
    public function single_price():int {
        return $this->row[SC::SINGLE_PRICE];
    }

     /**
     * 支払い価格を計算する。
     *
     * @return integer
     */
    public function total_price():int {
        return $this->row[SC::TOTAL_PRICE];
    }

    /**
     * 発送日を取得する。
     *
     * @return string
     */
    public function shipping_date():string {
        return $this->row[SC::SHIPT_DATE];
    }

    /**
     * 商品情報を取得する。
     *
     * @return array
     */
    public function items():array {
        if (!isset($this->row[SC::ITEMS])) {
            return [];
        }
        return $this->row[SC::ITEMS];
    }

    /**
     * 送料IDを取得する。
     *
     * @return integer
     */
    public function feeId():int {
        return $this->fee()[GlobalConstant::ID] ?? 0;
    }

    /**
     * 商品の合計金額（割引前）を取得する。
     *
     * @return integer
     */
    public function item_subtotal():int {
        return $this->row[SC::ITEM_SUBTOTAL];
    }


    /**
     * クーポン割引額を取得する。
     *
     * @return integer
     */
    public function discount_amount():int {
        return $this->row[SC::DISCOUNT_AMOUNT];
    }

    /**
     * 最終請求金額（割引後）を取得する。
     *
     * @return integer
     */
    public function grand_total():int {
        return $this->row[SC::GRAND_TOTAL];
    }

    /**
     * 送料情報を取得する。
     *
     * @return array
     */
    public function fee():array {
        return $this->row[SC::FEE];
    }

    /**
     * 商品種類数を取得する。
     *
     * @return integer
     */
    public function item_count():int {
        return $this->row[SC::ITEM_COUNT];
    }

    /**
     * 未登録の商品種類数を取得する。
     * @return integer
     */
    public function unregistered_item_count():int {
        $unregistered = array_filter($this->items(), function($item) {
            return !$item[SC::IS_REGISTERED];
        });
        return count($unregistered);
    }
}
