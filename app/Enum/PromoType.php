<?php
namespace App\Enum;

enum PromoType:string {
    case JPWARKER = 'jpwalker';
    case BOOSTER_FAN = 'boosterfun';
    case DRAFT = 'draft';
    case BUYABOX = 'buyabox';
    case TEXTURED = 'textured';
    case BUNDLE = 'bundle';
    case PROMOPACK = 'promopack';
    case OTHER = 'other';

    public function text() {
        return match($this) {
            self::JPWARKER => '絵違い',
            self::BOOSTER_FAN => 'ブースターファン',
            self::BUYABOX => 'BOXプロモ特典',
            self::TEXTURED => 'ショーケース',
            self::BUNDLE => 'バンドル',
            self::DRAFT => '',
            self::PROMOPACK => 'プロモカード',
            self::OTHER => 'その他'
        };
    }

    public static function match($card)
    {
        $key = 'promoTypes';
        if (!array_key_exists($key, $card)) {
            return self::DRAFT;
        }

        $promoarray = $card[$key];
        if (empty($promoarray)) {
            return self::DRAFT;
        }
        $beforeType = current($promoarray);
        $afterType = PromoType::tryFrom($beforeType);
        if (!is_null($afterType)) {
            return $afterType;
        }
        return self::OTHER;
    }
}
?>