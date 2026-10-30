<?php
namespace App\Services\Shipt\Platform;

use App\Services\Shipt\Platform\AbstractShopStrategy;
use Carbon\Carbon;
use Override;

/**
 * メルカリShopsの配送方法に関するクラス
 */
class MercariStrategy extends AbstractShopStrategy
{
    #[Override]
    protected function miniThreshold():int
    {
        return 1499;
    }

    #[Override]
    protected function getClickpostMinPrice():int
    {
        return 1499;
    }

    #[Override]
     protected function getClickpostMaxPrice():int
    {
        return 9999;
    }

    #[Override]
    public function isRakuRaku(string $zipcode) {
        return $zipcode === '100-0000';
    }

    #[Override]
    public function isRegisteredMail(int $price) {
        return $price >= 11000 - 460;
    }

    #[Override]
    public function isNekopost(int $price, Carbon $shiptDate) {
        $date2026 = Carbon::parse('2026-10-21');
        if ($shiptDate->lt($date2026)) {
            return false;
        }
        return $price >= $this->getClickpostMinPrice() && $price <= $this->getClickpostMaxPrice();
    }

}
