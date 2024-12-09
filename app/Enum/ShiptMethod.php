<?php
namespace App\Enum;
/**
 * 発送方法を示すEnum
 */

use App\Models\Shipping;

enum ShiptMethod: string {
    case MINI = 'ミニレター';
    case CLICK = 'クリックポスト';
    case REGISTER_MAIL = '簡易書留';

    /**
     * 金額に応じて発送方法をDBから取得する。
     *
     * @param integer $price
     * @return Shipping
     */
    public static function findByPrice(int $price) {
        $method =  self::MINI; 
        if ($price >= 1500 && $price < 9999) {
            $method = self::CLICK;
        } else if ($price >= 10000) {
            $method = self::REGISTER_MAIL;
        }
        $method = Shipping::findByMethod($method->value);
        return $method;
    }
}