<?php
namespace App\Factory;

use App\Services\json\Scryfall\ScryfallArtCard;
use App\Services\json\Scryfall\ScryfallCard;
use App\Services\json\Scryfall\ScryfallTransformCard;

/**
 * ScryfallCardオブジェクトを生成するクラス
 */
class ScryfallCardFactory {

    public static function create(array $json):ScryfallCard
    {
        switch ($json['layout'] ?? null) {
            case 'transform':
            case 'reversible_card':
            case 'modal_dfc':
                return new ScryfallTransformCard($json);
            case 'art_series':
                return new ScryfallArtCard($json);
            default:
                return new ScryfallCard($json);
        }
    }
}
