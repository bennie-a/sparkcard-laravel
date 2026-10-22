<?php

/**
 * ショップ別配送方法の判別に関する基盤クラス
 * @version 6.1.0
 */
abstract class AbstractShopStrategy {

    public function isMiniletter(int $price, Carbon $shiptDate) {
        return $price <= $this->miniThreshold();
    }

    protected function miniThreshold() {
        return 0;
    }

    public function isClickpost(int $price, Carbon $shiptDate) {
        return false;
    }

    public function isRakuRaku(int $price, Carbon $shiptDate) {
        return false;
    }

    public function isRegisteredMail(int $price, Carbon $shiptDate) {
        return false;
    }

    public function isDirect(int $price) {
        return false;
    }

    public function isNekopost(int $price) {

    }

    protected function isBoosterPack() {

    }
}
