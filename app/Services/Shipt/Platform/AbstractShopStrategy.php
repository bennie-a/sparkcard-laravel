<?php
namespace App\Services\Shipt\Platform;
use App\Models\CardInfo;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * ショップ別配送方法の判別に関する基盤クラス
 * @version 6.1.0
 */
abstract class AbstractShopStrategy {

    public function isMiniletter(int $price, Collection $orderItems, Carbon $shiptDate) {
        if ($this->isBoosterPack($orderItems)) {
            return false;
        }
        return $price >= 50 &&$price <= $this->calcLetterMaxPrice($shiptDate);
    }

    /**
     * ミニレターで送れる最高価格を計算する。
     *
     * @param Carbon $shiptDate
     * @return integer
     */
    protected function calcLetterMaxPrice(Carbon $shiptDate):int
    {
        $fee = $this->isBeforeOrOn20241001($shiptDate) ? 63 : 85;
        return $this->miniThreshold() - $fee;
    }

    protected function miniThreshold():int
    {
        return 0;
    }

    public function isClickpost(int $price, Collection $orderItems, Carbon $shiptDate) {
        if ($this->isBoosterPack($orderItems)) {
            return true;
        }
        return $price >= $this->calcClickPostMinPrice($shiptDate)
                                            && $price <= $this->calcClickPostMaxPrice($shiptDate);
    }

        /**
     * クリックポストで送れる最高価格を計算する。
     *
     * @param Carbon $shiptDate
     * @return integer
     */
    protected function calcClickPostMinPrice(Carbon $shiptDate):int
    {
        $fee = $this->isBeforeOrOn20261001($shiptDate) ? 185 : 240;
        return $this->getClickpostMinPrice() - $fee;
    }

    /**
     * クリックポストで送れる最高価格を計算する。
     *
     * @param Carbon $shiptDate
     * @return integer
     */
    protected function calcClickPostMaxPrice(Carbon $shiptDate):int
    {
        $fee = $this->isBeforeOrOn20261001($shiptDate) ? 185 : 240;
        return $this->getClickpostMaxPrice() - $fee;
    }

    /**
     * クリックポストの最低価格を取得する。
     *
     * @return integer
     */
    protected function getClickpostMinPrice():int
    {
        return 0;
    }

    /**
     * クリックポストの最高価格を取得する。
     *
     * @return integer
     */
    protected function getClickpostMaxPrice():int
    {
        return 0;
    }

    /**
     * らくらくメルカリ便か判定する。
     *
     * @param string $zipcode
     * @return boolean
     */
    public function isRakuRaku(string $zipcode) {
        return false;
    }

    /**
     * 簡易書留か判定する。
     *
     * @param integer $price
     * @return boolean
     */
    public function isRegisteredMail(int $price) {
        return false;
    }

    /**
     * 「手渡し」かどうか判定するクラス
     * @return bool
     */
    public function isDirect():bool {
        return false;
    }

    /**
     * ネコポスか判定する。
     *
     * @param integer $price
     * @param Carbon $shiptDate
     * @return boolean
     */
    public function isNekopost(int $price, Carbon $shiptDate) {
        return false;
    }

    /**
     * 灯争大戦ブースターパックが含まれているか検証する。
     *
     * @param Collection $orderItems
     * @return boolean
     */
    private function isBoosterPack(Collection $orderItems):bool {
        return $orderItems->where('stockpile.cardinfo.name', '灯争大戦ブースターパック')->isNotEmpty();
    }

    /**
     * 日付が2024/10/1より前か判定する。
     *
     * @param Carbon $date
     * @return boolean
     */
    public function isBeforeOrOn20241001(Carbon $date) {
        $date2024 = Carbon::parse('2024-10-01');
        return $date->lt($date2024);
    }

    /**
     * 日付が2026/10/1より前か判定する。
     *
     * @param Carbon $date
     * @return boolean
     */
    public function isBeforeOrOn20261001(Carbon $date) {
        $date2026 = Carbon::parse('2026-10-01');
        return $date->lt($date2026);
    }
}
