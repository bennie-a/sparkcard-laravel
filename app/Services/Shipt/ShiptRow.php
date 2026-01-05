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

    public function shipping_date() {
        $date = $this->row[SC::SHIPPING_DATE];
        return CarbonFormatUtil::assignTodayIfMissing($date);
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

    private int $shipment = 0;

    /**
     * 注文枚数を取得する。
     *
     * @return int
     */
    public function shipment():int {
        if ($this->shipment == 0) {
            // 例: "6枚セット", "3枚 セット" など両方に対応
            if (preg_match('/(\d+)枚\s*セット/u', $this->product_name(), $matches)) {
                $this->shipment = (int) $matches[1];
            } else {
                $this->shipment = (int)$this->row[SC::QUANTITY];
            }
        }
        return $this->shipment;
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
     *
     * @return int
     */
    public function single_price():int {
        $price = $this->total_price() / $this->shipment();
        return round($price);
    }

    /**
     * 支払い価格を計算する。
     *
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

    /**
     * 商品名から各種情報を抽出する。
     * @deprecated 5.1.0
     * @return void
     */
    private function extract() {
        $productName = $this->product_name();
        if (preg_match('/^【(?<setcode>.+?)】(?:【(?<foil>Foil)】)?(?<name>.+?)(?:≪(?<promotype>.+?)≫)?\[(?<lang>[A-Z]{2})]/u', $productName, $matches)) {            $this->setcode = $matches[SC::SETCODE];
            $this->setcode = $matches[SC::SETCODE];
            $this->cardname =  $matches[GlobalConstant::NAME];
            $this->promotype = $matches[CardConstant::PROMOTYPE] ?? '';
            $this->language = $matches[SC::LANG];
            $this->isFoil  = $matches['foil'] === 'Foil';
        } else {
            // パースに失敗した場合
            $this->setcode = $this->cardname = $this->promotype = $this->language = '';
            $this->isFoil = false;
        }
    }
}
