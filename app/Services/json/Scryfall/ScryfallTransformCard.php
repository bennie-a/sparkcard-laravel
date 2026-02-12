<?php
namespace App\Services\json\Scryfall;
use App\Services\json\Scryfall\ScryfallCard;

/**
 * Scryfall APIの両面カードクラス
 */
class ScryfallTransformCard extends ScryfallCard {

    public function imageurl() {
        $imageuris = $this->getJson()['card_faces'][0]['image_uris'];
        return $imageuris;
    }
}
