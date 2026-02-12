<?php
namespace App\Services\Shipt;
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
}
