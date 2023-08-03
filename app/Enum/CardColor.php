<?php
namespace App\Enum;

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
    case LAND = "Land";
    case UNDEFINED = "E";

    public function text() {
        return match($this) {
            self::RED => "赤",
            self::WHITE => "白",
            self::BLACK => "黒",
            self::GREEN => "緑",
            self::BLUE => "青",
            self::MULTI => "多色",
            self::LESS => "無色",
            self::ARTIFACT => "アーティファクト",
            self::LAND => "土地",
            self::UNDEFINED => "不明"
        };
    }

    /**
     * Undocumented function
     *
     * @param string $color
     * @return CardColor
     */
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
                $color = [];
                break;
            case CardColor::LAND:
                $color = [];
                break;
            case CardColor::LESS:
                $color = ["not-white", "not-blue", "not-black", "not-red", "not-green"];
                break;
        }
        return $color;
    }

    
    public function colorMulti() {
        $ope = "not";
        switch($this) {
            case CardColor::MULTI:
                $ope = "must";
                break;
            case CardColor::ARTIFACT:
                $ope = "able";
                break;
            case CardColor::LAND:
                $ope = "able";
                break;
            case CardColor::LESS:
                $ope = "must";
        }
        return $ope;
    }

    /**
     * Wisdom Guild.comの検索パラメータ(色の論理和)を返す。
     * @deprecated 2.0.0
     * @return "and"|"or"
     */
    public function colorOpe() {
        if ($this == CardColor::MULTI || $this == CardColor::LESS) {
            return "and";
        }
        return "or";
    }

    /**
     * Wisdom Guild.comの検索パラメータ(カードタイプ)を返す。
     * @deprecated 2.0.0
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
            case CardColor::LAND:
                $type = ["land"];
        }
        return $type;
    }

    /**
     * Wisdom Guild.comの検索パラメータ(カードタイプの論理和)を返す。
     * @deprecated 2.0.0
     */
    public function cardtypeOpe() {
        if ($this == CardColor::LESS) {
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
        $cardtype = current($types);
        // 無色
        if (!array_key_exists($colorKey, $card)) {
            return CardColor::LESS;
        } 
        $colorArray = $card[$colorKey];
        return self::findColor($colorArray, $cardtype);
    }

    public static function findColor(array $color, string $cardtype) {
        // アーティファクト
        if (strpos($cardtype, 'Artifact') !== false) {
            return CardColor::ARTIFACT;
        }
        // 土地
        if (strcmp($cardtype, "Land") == 0) {
            return CardColor::LAND;
        }

        // 無色
        if (empty($color)) {
            return CardColor::LESS;
        } 

        // 多色
        if (count($color) > 1) {
            return CardColor::MULTI;
        }
        // 単色
        return CardColor::tryFrom(current($color));
    }
}