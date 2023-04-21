<?php
namespace App\Services;

use App\Libs\MtgJsonUtil;
use App\Models\Promotype;
use app\Services\json\AbstractCard;

/**
 * 特別版カードの仕様を特定するクラス
 */
class PromoService {
    public function find(AbstractCard $cardtype) {
        $promoValue = $cardtype->promotype();
        // boosterfanの場合はframeeffectを取得する。
        if (strcmp($promoValue, 'boosterfun') == 0) {
            $promoValue = $cardtype->frameEffects();
        }
        $promo = Promotype::findCardByAttr($promoValue);
        if (empty($promo)) {
                throw new NoPromoTypeException($cardtype->getJson()['name'], $cardtype->number());
        }
        return $promo->name;
    }

 /**
     * 
     * jsonの'promoTypes'の最初の項目を取得する。
     *
     * @param [type] $c
     * @return string
     */
    private function getPromoValue($promoarray) {
        $filterd = array_filter($promoarray, function($p) {
            return $p != 'textured';
            // return in_array($p, PromoTypeEnum::TEXTURED->value()) == false;
        });
        return current($filterd);
    }
}