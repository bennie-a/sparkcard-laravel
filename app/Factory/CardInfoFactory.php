<?php
namespace App\Factory;

use App\Libs\MtgJsonUtil;
use App\Services\json\ExcludeCard;
use App\Services\json\FullartLand;
use App\Services\json\JpCard;
use App\Services\json\JpLimitedCard;
use App\Services\json\JpNoMultiIdCard;
use App\Services\json\NoJpCard;
use App\Services\json\PhyrexianCard;
use App\Services\json\PlistCard;
use App\Services\json\StarterCard;
use App\Services\json\TransformCard;

/**
 * JSONファイルに記載されたカード情報の形式に沿って
 * オブジェクトを作成するクラス
 */
class CardInfoFactory {

    /**
     * JSONファイルに記載されたカード情報の形式に沿って
     * オブジェクトを作成する。
     *
     * @param array $json
     * @return AbstractCard
     */
    public static function create($json) {
        $lang = $json['language'];
        $langArray = ['English' => NoJpCard::class, 'Japanese' => JpLimitedCard::class,
                         'Phyrexian' => PhyrexianCard::class];
        if (!MtgJsonUtil::hasKey($lang, $langArray) || self::isOnlineOnly($json)) {
            return new ExcludeCard($json);
        }
        $layout = $json["layout"];
        if (strcmp($layout, "transform") == 0) {
            return new TransformCard($json);
        }

        $class = $langArray[$lang];

        // カードタイプがフルアートか判別する。
        $cardtypes = $json["types"];
        if (MtgJsonUtil::isTrue("isFullArt", $json) && current($cardtypes) == "Land") {
            $class = FullartLand::class;
        }
        if ($class != NoJpCard::class) {
            $obj = new $class($json);
            return $obj;
        }
        $setCode = $json['setCode'];
        // ザ・リスト
        if (strcmp($setCode, 'PLIST') == 0) {
            return new PlistCard($json);
        }
        // 初期セット
        if (self::isStarterSet($setCode)) {
            if (self::hasJp($json)) {
                return new StarterCard($json);
            } else {
                return new ExcludeCard($json);
            }
        }
        // 日本語版の検索
        if (self::hasJp($json)) {
            return new JpCard($json);
        }
        return new NoJpCard($json);
    }

    /**
     * カード情報がオンライン限定か判定する。
     *
     * @param array $json
     * @return boolean
     */
    private static function isOnlineOnly($json) {
        return MtgJsonUtil::isTrue('isOnlineOnly', $json);
    }


    /**
     * foreignDataに日本語データが存在するか判定する。
     *
     * @param array $foreignData
     * @return boolean
     */
    private static function hasJp($json) {
        $foreignData = self::foreignData($json);
        if (is_null($foreignData)) {
            return false;
        }
        $target = MtgJsonUtil::extractJp($foreignData);
        return count($target) != 0;
    }

    /**
     * json内の'foreignData'のデータを取得する。
     *
     * @param array $json
     * @return array or null
     */
    private static function foreignData($json) {
        $key = 'foreignData';
        return MtgJsonUtil::isEmpty($key, $json) ? null : $json[$key];
    }

    /**
     * セット名が初期セットか判定する。
     *
     * @param string $setCode
     * @return boolean
     */
    private static function isStarterSet($setCode) {
        $starter = ['3ED', '4ED', '4ED', '5ED', '6ED', '7ED', '8ED'];
        return in_array($setCode, $starter);
    }


}
?>