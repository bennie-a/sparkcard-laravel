<?php
namespace App\Services\Shipt\Platform;
use App\Models\CardInfo;
use Carbon\Carbon;

/**
 * ショップ別配送方法の判別に関する基盤クラス
 * @version 6.1.0
 */
abstract class AbstractShopStrategy {

    public function isMiniletter(int $price, Carbon $shiptDate) {
        return $price >= 50 &&$price <= $this->miniThreshold();
    }

    protected function miniThreshold() {
        return 0;
    }

    public function isClickpost(int $price, Carbon $shiptDate) {
        return false;
    }

    public function isRakuRaku(int $price, string $zipcode) {
        return false;
    }

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

    public function isNekopost(int $price) {

    }

    protected function isBoosterPack(CardInfo $info) {

    }
}
