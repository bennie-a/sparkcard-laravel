<?php
namespace App\Libs;
use App\Services\Constant\StockpileHeader as Header;
/**
 * カード情報をJSON形式に変換するUtilクラス
 */
class CardInfoJsonUtil {

    /**
     * Foil情報を配列にして返す。
     *
     * @param boolean $isFoil
     * @param string $foiltype
     * @return array
     */
    public static function toFoil(bool $isFoil, string $foiltype):array {
        return [
                'is_foil' => $isFoil,
                Header::NAME => $foiltype == '通常版' ? '' :$foiltype
            ];
    }
}