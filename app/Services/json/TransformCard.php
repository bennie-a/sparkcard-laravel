<?php
namespace App\Services\json;

use App\Enum\CardColor;
use App\Services\Constant\CardConstant as Con;

/**
 * 両面カード用クラス
 */
class TransformCard extends JsonCard {

    /**
     * multiverse_idを返す
     *@see AbstractCard::multiverseId()
     * @return void
     */
    public function multiverseId() {
            return 0;
    }

    /**
     * カードの表面(A面)/裏面(B面)を取得する。
     *
     * @return string "a" or "b"
     */
    public function side() {
        return $this->json[Con::SIDE];
    }

        /**
     * 色コードを取得する。
     * @see AbstractCard::color()
     * @return string
     */
    public function color() {
        if (empty($this->color)) {
            $colors = $this->getJson()['colorIdentity'];
            $types =$this->getJson()["types"];
            $cardtype = current($types);
            $colortype = CardColor::findColor($colors, $cardtype);
            $this->color = $colortype->value;
        }
        return $this->color;
    }
    /**
     * 除外したいカード情報を検出する。
     * @override AbstractCard
     * @return boolean
     */
    public function isExclude() {
        return strcmp($this->side(), "a") != 0;
    }
}
?>