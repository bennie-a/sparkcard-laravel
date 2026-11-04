<?php
namespace App\Services\Shipt\Platform;

use App\Services\Shipt\Platform\AbstractShopStrategy;

/**
 * BASEショップの配送方法に関するクラス
 */
class BaseStrategy extends AbstractShopStrategy
{
    #[Override]
    protected function miniThreshold():int
    {
        return 4999;
    }

        #[Override]
    protected function getClickpostMinPrice():int
    {
        return 5000;
    }

    #[Override]
     protected function getClickpostMaxPrice():int
    {
        return 20000;
    }
}
