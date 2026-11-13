<?php
namespace App\Services\Shipt;
use App\Services\Constant\CardConstant;
use App\Services\Constant\GlobalConstant;
use App\Services\Constant\ShiptConstant as SC;
use App\Libs\CarbonFormatUtil;

/**
 * 出荷CSV1件分のクラス
 */
class ShiptRow {

    protected array $row;

    public function __construct(int $number, array $row)
    {
        $this->number = $number + 2;
        $this->row = $row;
    }

    /**
     * 行番号を取得する。
     *
     * @return int
     */
    public function number():int {
        return $this->number;
    }

    public function buyer() {
        return $this->row[SC::BUYER];
    }

    public function product_name() {
        return $this->row[$this->product_name_header()];
    }

    /**
     * 商品名を取得する列のヘッダーを取得する。
     *
     * @return string
     */
    protected function product_name_header() {
        return SC::PRODUCT_NAME;
    }

    public function postal_code() {
        return $this->row[SC::POSTAL_CODE];
    }

    public function address() {
        $address = $this->row[SC::STATE].$this->row[SC::CITY].
                        $this->row[SC::ADDRESS_1];
        if (!empty($this->row[SC::ADDRESS_2])) {
            $address.= " ". $this->row[SC::ADDRESS_2];
        }
        return $address;
    }

    /**
     * 商品価格を取得する。
     *
     * @return int
     */
    public function product_price():int {
        return (int)$this->row[SC::PRODUCT_PRICE];
    }

    /**
     * 1枚あたりの単価を計算する。
     *@deprecated 5.2.0
     * @return int
     */
    public function single_price():int {
        $price = $this->total_price() / $this->shipment();
        return round($price);
    }

    /**
     * 支払い価格を計算する。
     * @deprecated 5.2.0
     * @return integer
     */
    public function total_price():int {
        return $this->product_price() - $this->discount();
    }

    /**
     * クーポン割引額を取得する。
     *
     * @return int
     */
    public function discount():int {
        return (int)$this->row[SC::DISCOUNT_AMOUNT];
    }

    public function order_id() {
        return $this->row[SC::ORDER_ID];
    }
}
