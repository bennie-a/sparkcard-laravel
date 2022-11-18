<?php
namespace App\Factory;

use App\Services\json\JpCard;
use App\Services\json\JpLimitedCard;
use App\Services\json\NoJpCard;
use App\Services\json\OnlineCard;
use App\Services\json\PhyrexianCard;
use App\Services\json\PlistCard;

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
        if (strcmp($json['setCode'], 'PLIST') == 0) {
            return new PlistCard($json);
        }
        if (array_key_exists('isOnlineOnly', $json) && $json['isOnlineOnly'] == 'true') {
            return new OnlineCard($json);
        }
        $enname = $json['name'];
        $lang = $json['language'];
        if ($lang == 'Japanese') {
            return new JpLimitedCard($json);
        } else if ($lang == 'Phyrexian') {
            return new PhyrexianCard($json);
        }
        $isForeign = array_key_exists('foreignData', $json);
        if ($isForeign && !empty($json['foreignData']) && self::hasJp($json['foreignData'])) {
            return new JpCard($json);
        }
        return new NoJpCard($json);
    }

    /**
     * foreignDataに日本語が存在するか判定する。
     *
     * @param array $foreignData
     * @return boolean
     */
    private static function hasJp($foreignData) {
        $target = array_filter($foreignData, function($f) {
            return $f['language'] == 'Japanese';
        });
        return count($target) != 0;
    }
}
?>