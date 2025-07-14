<?php
namespace App\Libs;
use App\Services\Constant\StockpileHeader as Header;
use App\Services\Constant\CardConstant as Con;
use App\Services\Constant\GlobalConstant;

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

    public static function setPromoInfo($array, $promotypeId, $promoName): array {
        $array[Con::PROMOTYPE] = [
            GlobalConstant::ID => $promotypeId,
            GlobalConstant::NAME => $promoName
        ];
        return $array;
    }
}