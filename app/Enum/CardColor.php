<?php
namespace App\Enum;

use Illuminate\Support\Facades\Log;

/**
 * カードの色を示すenum
 */
enum CardColor:string {
    case RED = "R";
    case WHITE = "W";
    case BLACK = "B";
    case GREEN = "G";
    case BLUE = "U";
    case MULTI = "M";
    case LESS = "L";
    case ARTIFACT = "A";

    public function text() {
        return match($this) {
            self::RED => "赤",
            self::WHITE => "白",
            self::BLACK => "黒",
            self::GREEN => "緑",
            self::BLUE => "青",
            self::MULTI => "多色",
            self::LESS => "無色",
            self::ARTIFACT => "アーティファクト"
        };
    }

    public static function matchByString(string $color):CardColor {
        $target = array_filter(CardColor::cases(), function($c) use ($color){
            $lower = $c->toLowerCase();
            return strcmp($lower, $color) == 0;
        });
        $keys = array_keys($target);
        return $target[$keys[0]];
    }

    /**
     * Wisdom Guild.comの検索パラメータ(色)を返す。
     * @return $color 色
     */
    public function color():array {
        $color =  [$this->toLowerCase()];
        switch($this) {
            case CardColor::MULTI:
                $color = ["non-colorless"];
                break;
            case CardColor::ARTIFACT:
                $color = ["not-white", "not-blue", "not-black", "not-red", "not-green"];
                break;
            case CardColor::LESS:
                $color = ["not-white", "not-blue", "not-black", "not-red", "not-green"];
                break;
        }
        return $color;
    }

    
    public function colorMulti() {
        if ($this == CardColor::MULTI) {
            return "must";
        }
        return "not";
    }

    /**
     * Wisdom Guild.comの検索パラメータ(色の論理和)を返す。
     * @return "and"|"or"
     */
    public function colorOpe() {
        if ($this == CardColor::MULTI) {
            return "and";
        }
        return "or";
    }

    /**
     * Wisdom Guild.comの検索パラメータ(カードタイプ)を返す。
     */
    public function cardtype() {
        $type = [];
        switch($this) {
            case CardColor::ARTIFACT:
                $type = ["artifact"];
                break;
            case CardColor::LESS:
                $type = ["artifact", "land"];
                break;
        }
        return $type;
    }

    /**
     * Wisdom Guild.comの検索パラメータ(カードタイプの論理和)を返す。
     */
    public function cardtypeOpe() {
        if ($this == CardColor::ARTIFACT) {
            return "nor";
        }
        return "and";
    }

    /**
     * Enumのnameを小文字化する。
     */
    function toLowerCase() {
        return mb_strtolower($this->name);
    }

    /**
     * カード情報からCardColorを取得する。
     * 
     */
    public static function match(array $card) {
        $colorKey = "colors";
        $types = $card["types"];
        if (strcmp($types[0], "Artifact") == 0) {
            return CardColor::ARTIFACT;
        }

        // 無色
        if (!array_key_exists($colorKey, $card)) {
            return CardColor::LESS;
        } 

        $colorArray = $card[$colorKey];
        // 多色
        if (count($colorArray) > 1) {
            return CardColor::MULTI;
        }
        // 単色
        return CardColor::tryFrom($colorArray[0]);
    }
}