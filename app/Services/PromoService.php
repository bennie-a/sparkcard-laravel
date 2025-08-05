<?php
namespace App\Services;

use App\Exceptions\api\NoPromoTypeException;
use App\Models\Promotype;
use App\Services\Constant\StockpileHeader;
use app\Services\json\AbstractCard;

use function PHPUnit\Framework\isEmpty;

/**
 * 特別版カードの仕様を特定するクラス
 */
class PromoService {
    public function find(AbstractCard $cardtype) {
        $promoValue = $cardtype->promotype();

        $promo = Promotype::findCardByAttr($promoValue);
        if (empty($promo)) {
                throw new NoPromoTypeException($cardtype->number(), $promoValue);
        }
        return $promo->id;
    }

    /**
     * 入力された条件を元に検索する。
     *
     * @param array $condition
     * @return array
     */
    public function fetch(array $condition) {
        $setcode = $condition[StockpileHeader::SETCODE];
        $result = Promotype::findBySetCode($setcode);
        $filtered = $result->filter(function($r) use ($setcode) {
            return $r->setcode == $setcode;
        });
        if ($filtered->isEmpty()) {
            return $filtered;
        }
        return $result;
    }
}