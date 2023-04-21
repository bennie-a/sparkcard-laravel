<?php
namespace App\Services;

use App\Libs\MtgJsonUtil;
use App\Models\Promotype;

/**
 * 特別版カードの仕様を特定するクラス
 */
class PromoService {
    public function find($card) {
        $promo = Promotype::findCardByAttr(self::getPromoValue($card));
        // boosterfanの場合はframeeffectを取得する。
        if (empty($promo)) {
            $promoEnum = PromoTypeEnum::match($card);
            if ($promoEnum == PromoTypeEnum::OTHER) {
                throw new NoPromoTypeException(($card['name']), json['number']);
            }
            return $promoEnum->text();
        }
        return $promo->name;
    }

 /**
     * 
     * 要別クラス化
     *
     * @param [type] $c
     * @return string
     */
    private function getPromoValue($c) {
        $promokey = 'promoTypes';
        if (!MtgJsonUtil::hasKey($promokey, $c)) {
            return 'draft';
        };
        $promoarray = array_filter($c[$promokey], function($p) {
            return $p != 'textured';
            // return in_array($p, PromoTypeEnum::TEXTURED->value()) == false;
        });
        return current($promoarray);
    }
}