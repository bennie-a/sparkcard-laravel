<?php
namespace App\Services\Shipt;

use App\Enum\ShiptMethod;
use App\Enum\ShopPlatform;
use App\Exceptions\api\Shipt\NFShiptMethodException;
use App\Repositories\Api\Shipt\ShiptMethodRepository;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use App\Services\Constant\GlobalConstant as GC;
use App\Services\Constant\ShiptConstant as SC;

/**
 * 配送方法に関するServiceクラス
 */
class ShiptMethodService {

    public function __construct(private ShiptMethodRepository $repo) {

    }

    /**
     * 送料IDと送料を特定する。
     *
     * @param  ShopPlatform $platform
     * @param int $total
     * @param string $shiptDate
     * @return array
     */
    public function determine(ShopPlatform $platform, int $total, string $shiptDate):array
    {
        $method = ShiptMethod::UNDEFINED;
        $sdate = new Carbon($shiptDate);
        if ($platform === ShopPlatform::DIRECT) {
            $method = ShiptMethod::DIRECT;
        }

        if ($method === ShiptMethod::UNDEFINED) {
            throw new NFShiptMethodException();
        }

        $shipping = $this->repo->findByMethod($method, false);
        return [GC::ID => $shipping->id, SC::METHOD => $method, SC::PRICE => $shipping->price];
    }
}
