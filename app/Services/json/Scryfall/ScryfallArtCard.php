<?php
namespace App\Services\json\Scryfall;

use App\Enum\CardColor;
use App\Services\json\Scryfall\ScryfallTransformCard;

/**
 * Scryfall.comから取得したアート・カード情報クラス
 */
class ScryfallArtCard extends ScryfallTransformCard {

    private const DRAFT = '通常版';

    private const FOIL = '箔押し';

    public function colors():array {
        return [CardColor::ART->value];
    }

    public function foiltype() {
        $json = $this->getJson();
        if ($json['foil'] && $json['nonfoil']) {
            return [self::DRAFT, self::FOIL];
        } else if ($json['foil']) {
            return [self::FOIL];
        } else if ($json['nonfoil']) {
            return [self::DRAFT];
        }
    }
}
