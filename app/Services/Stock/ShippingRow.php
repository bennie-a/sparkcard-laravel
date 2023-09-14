<?php
namespace App\Services\Stock;
use App\Services\Constant\StockpileHeader as Header;
use App\Services\Stock\Strategy\NoSetCodeStrategy;
use App\Services\Stock\Strategy\DefaultRowStrategy;
use Carbon\Carbon;

/**
 * 出荷CSV1件分のクラス
 */
class ShippingRow extends StockpileRow {
    
    private $setcode;

    private $cardname;

    private $language;
    public function __construct(int $number, array $row)
    {
        $this->number = $number + 2;
        $this->row = $row;
    }

    public function shipping_date() {
        $date = $this->row[Header::SHIPPING_DATE];
        $carbon = new Carbon();
        if (!empty($date)) {
            $carbon = new Carbon($date);
        }
        return $carbon;
    }

    public function buyer() {
        return $this->row[Header::BUYER];
    }

    public function product_name() {
        return $this->row[Header::PRODUCT_NAME];
    }

    public function postal_code() {
        return $this->row[Header::BILLING_POSTAL_CODE];
    }

    public function setcode()
    {
        if (empty($this->setcode)) {
            \preg_match("/(?<=【)[A-Z]+(?=】)/", $this->product_name(), $match);
            $this->setcode = current($match);
        }
        return $this->setcode;
    }

    /**
     * カード名を取得する。
     *
     * @return string
     */
    public function card_name():string {
        if (empty($this->cardname)) {
            $start = mb_strrpos($this->product_name(), "】") + 1;
            $length = mb_strpos($this->product_name(), "[") - $start;
            $this->cardname = mb_substr($this->product_name(), $start, $length, 'UTF-8');
        }
        return $this->cardname;
    }

    public function isFoil():bool {
        return strpos($this->product_name(), "【Foil】");
    }

    public function language() {
        if (empty($this->language)) {
            \preg_match("/(?<=\[)[A-Z]+(?=\])/", $this->product_name(), $match);
            $this->language = current($match);
        }
        return $this->language;
    }
    
    public function address() {
        $address = $this->row[Header::BILLING_STATE].$this->row[Header::BILLING_CITY]. 
                        $this->row[Header::BILLING_ADDRESS_1];
        if (!empty($this->row[Header::BILLING_ADDRESS_2])) {
            $address.= " ". $this->row[Header::BILLING_ADDRESS_2];
        }
        return $address;
    }

    public function product_price() {
        return $this->row[Header::PRODUCT_PRICE];
    }

    public function total_price() {
        return $this->product_price() * $this->quantity();
    }

    public function order_id() {
        return $this->row[Header::ORDER_ID];
    }
}