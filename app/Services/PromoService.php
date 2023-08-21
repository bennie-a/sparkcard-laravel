<?php
namespace App\Services;

use App\Exceptions\NoPromoTypeException;
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
                throw new NoPromoTypeException($cardtype->getJson()['name'], $cardtype->number(), $promoValue);
        }
        return $promo->name;
    }

    public function findFoil(AbstractCard $cardtype) {

    }
}