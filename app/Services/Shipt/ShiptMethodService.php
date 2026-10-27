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
        [$method, $isDeleted] = $this->determineMethod($platform, $orderItems, $subtotal);
        if ($method === ShiptMethod::UNDEFINED) {
            $first = $orderItems->first();
            throw new NFShiptMethodException($first->order_id);
        }

        $shipping = $this->repo->findByMethod($method, $isDeleted);
        return [$shipping->id, $method, $shipping->price];
    }

    private function determineMethod(ShopPlatform $platform, Collection $orderItems, int $subtotal):array
    {
        $first = $orderItems->first();
        $sdate = new Carbon($first->shipping_date);
        $strategy = $this->getStrategy($platform);

        if ($strategy->isDirect()) {
            return [ShiptMethod::DIRECT, false];
        }
        if ($strategy->isRakuRaku($first->zip_code)) {
            return [ShiptMethod::RAKURAKU, false];
        }
        if ($strategy->isRegisteredMail($subtotal)) {
            return [ShiptMethod::REGISTER_MAIL, false];
        }
        if ($strategy->isNekopost($subtotal, $sdate)) {
            return [ShiptMethod::NEKOPOST, false];
        }
        if ($strategy->isMiniletter($subtotal, $orderItems, $sdate)) {
            return [ShiptMethod::MINI, $strategy->isBeforeOrOn20241001($sdate)];
        }
        if ($strategy->isClickpost($subtotal, $orderItems, $sdate)) {
            return [ShiptMethod::CLICK, $strategy->isBeforeOrOn20261001($sdate)];
        }

        return [ShiptMethod::UNDEFINED, false];
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
