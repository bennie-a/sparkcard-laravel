<?php
namespace App\Models\mtg;

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
    /**
     * カード情報からCardColorを取得する。
     * 
     */
    public static function match(array $card) {
        $colorKey = "colorIdentity";
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