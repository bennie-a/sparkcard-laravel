<?php
namespace App\Services\Shipt\Platform;

use App\Services\Shipt\Platform\AbstractShopStrategy;

/**
 * 直接取引からの配送方法に関するクラス
 */
class DirectStrategy extends AbstractShopStrategy
{

    public function isDirect():bool {
        return true;
    }
}
