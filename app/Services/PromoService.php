<?php
namespace App\Services;

use App\Exceptions\api\NoExpException;
use App\Exceptions\api\NoPromoTypeException;
use App\Models\Expansion;
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
        $isExist = Expansion::isExistByAttr($setcode);
        if (!$isExist) {
            throw new NoExpException($setcode);
        }
        $result = Promotype::findBySetCode($setcode);
        return $result;
    }
}