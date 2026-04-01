<?php
namespace App\Services\json\Scryfall;
use App\Services\json\Scryfall\ScryfallCard;
use App\Services\Constant\GlobalConstant as GCon;

/**
 * Scryfall APIの両面カードクラス
 */
class ScryfallTransformCard extends ScryfallCard {

    public function imageurl() {
        $face = $this->face();
        $imageuris = $face['image_uris'];
        return $imageuris;
    }

    public function enname(): string
    {
        $face = $this->face();
        return $face[GCon::NAME];
    }

    /**
     * カードの表面を取得する。
     *
     * @return array
     */
    protected function face() {
            $faces = $this->getJson()['card_faces'];
            return current($faces);
    }
}
