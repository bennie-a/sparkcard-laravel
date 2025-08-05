<?php
namespace App\Services\Stock;

use App\Services\Constant\CardConstant;
use App\Services\Constant\GlobalConstant;
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

    private $promotype;

    private $isFoil;

    public function __construct(int $number, array $row)
    {
        $this->number = $number + 2;
        $this->row = $row;
        $this->extract();
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
        return $this->row[$this->product_name_header()];
    }

    /**
     * 商品名を取得する列のヘッダーを取得する。
     *
     * @return string
     */
    protected function product_name_header() {
        return Header::PRODUCT_NAME;
    }

    public function postal_code() {
        return $this->row[Header::BILLING_POSTAL_CODE];
    }

    public function setcode()
    {
        return $this->setcode;
    }

    /**
     * カード名を取得する。
     *
     * @return string
     */
    public function card_name():string {
        return $this->cardname;
    }

    public function isFoil():bool {
        return $this->isFoil;
    }

    public function language() {
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
    
    public function promotype() {
        return $this->promotype;
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

    private function extract() {
        $productName = $this->product_name();
        if (preg_match('/^【(?<setcode>.+?)】(?:【(?<foil>Foil)】)?(?<name>.+?)(?:≪(?<promotype>.+?)≫)?\[(?<lang>[A-Z]{2})]/u', $productName, $matches)) {            $this->setcode = $matches[Header::SETCODE];
            $this->setcode = $matches[Header::SETCODE];
            $this->cardname =  $matches[GlobalConstant::NAME];
            $this->promotype = $matches[CardConstant::PROMOTYPE] ?? ''; 
            $this->language = $matches[Header::LANG];
            $this->isFoil  = $matches['foil'] === 'Foil';
        } else {
            // パースに失敗した場合
            $this->setcode = $this->cardname = $this->promotype = $this->language = '';
            $this->isFoil = false;
        }
    }
}