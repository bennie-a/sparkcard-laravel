<?php
namespace App\Factory;

use App\Services\json\ExcludeCard;
use App\Services\json\FullartLand;
use App\Services\json\JpCard;
use App\Services\json\JpLimitedCard;
use App\Services\json\NoJpCard;
use App\Services\json\PhyrexianCard;
use App\Services\json\PlistCard;
use App\Services\json\StarterCard;

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
        if (!array_key_exists($lang, $langArray)) {
            return new ExcludeCard($json);
        }
        $class = $langArray[$lang];

        // カードタイプがフルアートか判別する。
        $cardtypes = $json["types"];
        if (self::isTrue("isFullArt", $json) && current($cardtypes) == "Land") {
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
        // オンライン限定カード
        if (self::isOnlineOnly($json)) {
            return new ExcludeCard($json);
        }
        // 日本語カード
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
        return self::isTrue('isOnlineOnly', $json);
    }

    private static function isTrue($key, $json) {
        if (!array_key_exists($key, $json)) {
            return false;
        }
        return $json[$key] == 'true';
    }

    /**
     * foreignDataに日本語データが存在するか判定する。
     *
     * @param array $foreignData
     * @return boolean
     */
    private static function hasJp($json) {
        $isForeign = array_key_exists('foreignData', $json);
        if (!$isForeign) {
            return false;
        }
        $foreignData = $json['foreignData'];
        if (empty($foreignData)) {
            return false;
        }
        $target = array_filter($foreignData, function($f) {
            return $f['language'] == 'Japanese';
        });
        return count($target) != 0;
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