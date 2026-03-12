<?php

namespace App\Http\Resources\Api;

use App\Enum\CardColor;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\Constant\GlobalConstant as GCon;
use App\Services\Constant\CardConstant as CCon;
use App\Services\json\Scryfall\ScryfallCard;

/**
 * Scryfall APIから取得したカード情報を整形するResourceクラス
 * @since  5.2.2
 */
class ScryfallResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $card = new ScryfallCard($this->resource);
        $color = CardColor::findColor($card->colors(), $card->types());

        return [
            GCon::NAME => $card->name(),
            CCon::EN_NAME => $card->enname(),
            CCon::IMAGE_URL => $card->png(),
            CCon::MULTIVERSEID => $card->multiverseId(),
            CCon::COLOR => $color->value,
            // CCon::PROMOTYPE => $card->promotype(),
            // CCon::NUMBER => $card->number(),
            // CCon::SETCODE => $card->setcode(),
            ];
    }
            // return ['name' => $card->name(),
            //         'multiverse_id' => $card->multiverseId(),
            //         'en_name' => $card->enname(),
            //         'color' => $color->value,
            //         'promotype'=>$promotype,
            //         'imageurl' => $card->imageurl(),
            //         'number' => $card->number(),
            //         'setcode' => $card->setcode(),
            //         'reprint' => $card->reprint(),
            //         'foiltype' => $card->foiltype()
            // ];
}
