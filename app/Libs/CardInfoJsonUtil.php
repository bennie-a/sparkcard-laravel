<?php
namespace App\Libs;
use App\Services\Constant\StockpileHeader as Header;
use App\Services\Constant\CardConstant as Con;
use App\Services\Constant\GlobalConstant as GCon;

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
                GCon::NAME => $foiltype == '通常版' ? '' :$foiltype
            ];
        $array[Header::FOIL] = $foils;
        return $array;
    }
    /**
     * プロモ情報を配列に設定する。
     *
     * @param array $array
     * @param int $promotypeId
     * @param string $promoName
     * @return array
     */
    public static function setPromoInfo(array $array, int $promotypeId, string $promoName): array {
        $array[Con::PROMOTYPE] = [
            GCon::ID => $promotypeId,
            GCon::NAME => $promoName
        ];
        return $array;
    }
}
