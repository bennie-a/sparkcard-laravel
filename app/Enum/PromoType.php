<?php
namespace App\Enum;

use app\Libs\JsonUtil;
use FrameEffects;

enum PromoType:string {
    case JPWARKER = 'jpwalker';
    case BOOSTER_FAN = 'boosterfun';
    case DRAFT = 'draft';
    case BUYABOX = 'buyabox';
    case TEXTURED = 'textured';
    case BUNDLE = 'bundle';
    case PROMOPACK = 'promopack';
    case SHOWCASE = "showcase";
    case EXTENDEDART = "extendedart";
    case FULLART = "fullart";

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
            self::SHOWCASE => "ショーケース",
            self::EXTENDEDART => "拡張アート",
            self::FULLART => 'フルアート',

            self::OTHER => 'その他'
        };
    }

    public static function match($card)
    {
        $isFullArt = "isFullArt";
        if (self::hasKey($isFullArt, $card)) {
            return self::FULLART;            
        }
        $key = 'promoTypes';
        if (!self::isNotEmpty($key, $card)) {
            return self::DRAFT;
        }

        $promoarray = $card[$key];
        $typeword = self::excludeKeyword([self::TEXTURED->value], $promoarray);
        $promoType = PromoType::tryFrom(current($typeword));
        if ($promoType == self::BOOSTER_FAN) {
            $promoType = self::frameEffect($card);
        } 
        if (!is_null($promoType)) {
            return $promoType;
        }
        return self::OTHER;
    }

       /**
     * frameEffectsの内容から拡張アートかショーケースを判別する。
     *
     * @param array $card
     * @return Promotype
     */
    private static function frameEffect($card) {
        $key = 'frameEffects';
        if (!self::isNotEmpty($key, $card)) {
            return self::BOOSTER_FAN;
        }
        $effects = array_map(function($f) {
            $type = PromoType::tryFrom($f);
            return $type;
        }, $card[$key]);
        $effects = array_filter($effects, function($e) {
            return is_null($e) == false;
        });
        // $effects = self::excludeKeyword(["etched", "legendary"], $card[$key]);
        return current($effects);
    }

    private static function hasKey($key, $card) {
        return array_key_exists($key, $card);
    }

    private static function isNotEmpty($key, $card) {
        return self::hasKey($key, $card) && !empty($card[$key]);
    }

    private static function excludeKeyword($keyword, $array) {
        $promoarray = array_filter($array, function($p) use($keyword){
            return in_array($p, $keyword) == false;
        });
        return $promoarray;
    }
}
?>