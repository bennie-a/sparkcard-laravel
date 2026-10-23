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
        $method = ShiptMethod::MINI;
        $first = $orderItems->first();
        $sdate = new Carbon($first->shipping_date);
        $strategy = $this->getStrategy($platform);
        if ($strategy->isDirect()) {
            $method = ShiptMethod::DIRECT;
        }

        if ($strategy->isMiniletter($subtotal, $sdate)) {
            $method = ShiptMethod::MINI;
        }

        if ($strategy->isClickpost($subtotal, $sdate)) {
            $method = ShiptMethod::CLICK;
        }

        if ($strategy->isRegisteredMail($subtotal)) {
            $method = ShiptMethod::REGISTER_MAIL;
        }
        if ($strategy->isRakuRaku($subtotal, $first->zipcode)) {
            $method = ShiptMethod::RAKURAKU;
        }
        if ($strategy->isNekopost($subtotal)) {
            $method = ShiptMethod::NEKOPOST;
        }


        // if ($method === ShiptMethod::UNDEFINED) {
        //     throw new NFShiptMethodException();
        // }

        $shipping = $this->repo->findByMethod($method, false);
        return [GC::ID => $shipping->id, SC::METHOD => $method, SC::PRICE => $shipping->price];
    }

    private function getStrategy(ShopPlatform $platform)
    {
        return match($platform) {
            ShopPlatform::MERCARI => new MercariStrategy(),
            ShopPlatform::BASE => new BaseStrategy(),
            ShopPlatform::DIRECT => new DirectStrategy()
        };
    }
}
