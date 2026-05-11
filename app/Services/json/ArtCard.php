<?php
namespace App\Services\json;

use App\Enum\CardColor;

/**
 * アート・カード用クラス
 */
class ArtCard extends TransformCard {
    public function color() {
        return CardColor::ART->value;
    }

    /**
     * array_map用の処理を分離
     */
    protected function mapFoilType($f) {
        if ($f === 'nonfoil') {
            return '通常版';
        } else if ($f === 'signed') {
            return '箔押し';
        }
    }
}
