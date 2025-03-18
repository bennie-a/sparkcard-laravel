<?php
namespace App\Libs;
use App\Services\Constant\StockpileHeader as Header;
/**
 * カード情報をJSON形式に変換するUtilクラス
 */
class CardInfoJsonUtil {

    /**
     * Foil情報を配列に設定する。
     *
     * @param array $array
     * @param boolean $isFoil
     * @param string $foiltype
     * @return array
     */
    public static function setFoilInfo($array, bool $isFoil, string $foiltype):array {
        $foils = [
                'is_foil' => $isFoil,
                Header::NAME => $foiltype == '通常版' ? '' :$foiltype
            ];
        $array[Header::FOIL] = $foils;
        return $array;
    }
}