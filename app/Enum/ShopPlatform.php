<?php

namespace App\Enum;

/**
 * ショッププラットフォームに関するEnumクラス
 */
enum ShopPlatform: string
{
    case MERCARI = 'mercari';
    case BASE = 'base';
    case DIRECT = 'direct';

    public function orderPrefix()
    {
        return match($this) {
            ShopPlatform::MERCARI => 'order_',
            ShopPlatform::DIRECT => 'd2c_',
            ShopPlatform::BASE => ''
        };
    }

    /**
     * 注文番号からプラットフォームを判定する。
     *
     * @param string $orderId
     * @return ShopPlatform
     */
    public static function determine(string $orderId):ShopPlatform
    {
        foreach([self::MERCARI, self::DIRECT] as $p) {
            if (strpos($orderId, $p->orderPrefix()) === 0) {
                return $p;
            }
        }
        return ShopPlatform::BASE;
    }
}
