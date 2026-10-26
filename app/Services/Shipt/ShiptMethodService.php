<?php
namespace App\Services\Shipt;

use App\Enum\ShiptMethod;
use App\Enum\ShopPlatform;
use App\Exceptions\api\Shipt\NFShiptMethodException;
use App\Models\CardInfo;
use App\Repositories\Api\Shipt\ShiptMethodRepository;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use App\Services\Constant\GlobalConstant as GC;
use App\Services\Constant\ShiptConstant as SC;
use App\Services\Shipt\Platform\AbstractShopStrategy;
use App\Services\Shipt\Platform\BaseStrategy;
use App\Services\Shipt\Platform\DirectStrategy;
use App\Services\Shipt\Platform\MercariStrategy;
use Illuminate\Support\Collection;

/**
 * 配送方法に関するServiceクラス
 */
class ShiptMethodService {

    public function __construct(private ShiptMethodRepository $repo) {

    }

    /**
     *送料IDと送料を特定する。
     *
     * @param ShopPlatform $platform
     * @param Collection $orderItems
     * @return array
     */
    public function determine(ShopPlatform $platform, Collection $orderItems, int $subtotal):array
    {
        $method = ShiptMethod::UNDEFINED;
        $first = $orderItems->first();
        $sdate = new Carbon($first->shipping_date);
        $strategy = $this->getStrategy($platform);
        $isDeleted = false;

        if ($strategy->isDirect()) {
            $method = ShiptMethod::DIRECT;
        } else if ($strategy->isRakuRaku($first->zip_code)) {
            $method = ShiptMethod::RAKURAKU;
        } else if ($strategy->isRegisteredMail($subtotal)) {
            $method = ShiptMethod::REGISTER_MAIL;
        } else if ($strategy->isNekopost($subtotal, $sdate)) {
            $method = ShiptMethod::NEKOPOST;
        } else if ($strategy->isMiniletter($subtotal, $orderItems, $sdate)) {
            $method = ShiptMethod::MINI;
            $isDeleted = $strategy->isBeforeOrOn20241001($sdate);
        } else if ($strategy->isClickpost($subtotal, $orderItems, $sdate)) {
            $method = ShiptMethod::CLICK;
            $isDeleted = $strategy->isBeforeOrOn20261001($sdate);
        }

        if ($method === ShiptMethod::UNDEFINED) {
            throw new NFShiptMethodException($first->order_id);
        }

        $shipping = $this->repo->findByMethod($method, $isDeleted);
        return [GC::ID => $shipping->id, SC::METHOD => $method, SC::PRICE => $shipping->price];
    }

    /**
     * プラットフォーム別に条件クラスを取得する。
     *
     * @param ShopPlatform $platform
     * @return AbstractShopStrategy
     */
    private function getStrategy(ShopPlatform $platform):AbstractShopStrategy
    {
        return match($platform) {
            ShopPlatform::MERCARI => new MercariStrategy(),
            ShopPlatform::BASE => new BaseStrategy(),
            ShopPlatform::DIRECT => new DirectStrategy()
        };
    }
}
