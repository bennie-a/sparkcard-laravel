<?php
namespace App\Factory;

use App\Libs\MtgJsonUtil;
use App\Models\ExcludePromo;
use app\Services\json\AbstractCard;
use App\Services\json\AdventureCard;
use App\Services\json\BasicLand;
use App\Services\json\ExcludeCard;
use App\Services\json\JpCard;
use App\Services\json\JpLimitedCard;
use App\Services\json\JsonCard;
use App\Services\json\NoJpCard;
use App\Services\json\PhyrexianCard;
use App\Services\json\PlistCard;
use App\Services\json\StarterCard;
use App\Services\json\TransformCard;
use App\Services\Constant\CardConstant as Con;
use App\Services\Constant\CardConstant;

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
        if (self::isExclude($json)) {
            return new ExcludeCard($json);
        }
        // 2機能以上のカード
        if (MtgJsonUtil::hasKey(Con::SIDE, $json)) {
            return new TransformCard($json);
        }

        // 言語限定カード
        $class = match($json['language']) {
            'Japanese' =>JPLimitedCard::class,
            'Phyrexian' => PhyrexianCard::class,
            'English' => JsonCard::class
        };

        // 基本土地or冠雪土地
        $cardtypes = $json["types"];
        if (in_array('Land',  $cardtypes)) {

            $superTypes = $json["supertypes"];
            if (in_array('Snow', $superTypes)) {
                $class = JsonCard::class;
            } else if (in_array('Basic', $superTypes)) {
                $class = BasicLand::class;
            }
        }
        if ($class != JsonCard::class) {
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

        return new JsonCard($json);
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

    /** 除外カードか判別する
     *  @return bool
     */
    private static function isExclude($json):bool {
        // 除外するプロモタイプであるか判別する。
        if (MtgJsonUtil::hasKey(CardConstant::PROMOTYPES, $json)) {
            $promotypes = $json['promoTypes'];
            // logger()->debug('除外カードか判別', [$json[CardConstant::NUMBER]]);
            return ExcludePromo::existsByAttr($promotypes);
        }
        return self::isOnlineOnly($json) || self::isAdventure($json) || self::isExtendedArt($json);
    }

    /**
     * 出来事カードか判別する。
     *
     * @param array $json
     * @return boolean
     */
    private static function isAdventure($json) {
        return strcmp($json["type"], "Sorcery — Adventure") == 0;
    }


    private static function isExtendedArt($json) {
        if (MtgJsonUtil::hasKey(Con::FRAME_EFFECT, $json)) {
            return $json[Con::FRAME_EFFECT][0] === 'extendedart';
        }
        return false;
    }
}
?>