<?php

namespace App\Http\Resources;

use App\Enum\CardColor;
use App\Libs\CardInfoJsonUtil;
use App\Libs\MtgJsonUtil;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\Constant\StockpileHeader as Header;
use App\Services\Constant\CardConstant as Con;
use Illuminate\Http\Request;

class CardInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray(Request $request)
    {
        $array =  [
            Con::ID => $this->id,
            Con::NAME => $this->name,
            Con::EXP => [Con::NAME => $this->exp_name, Con::ATTR => $this->exp_attr],
            Con::NUMBER => $this->number,
            Con::COLOR => CardColor::tryFrom($this->color_id)->text(),
            'image_url' => $this->image_url,
            Header::CONDITION => $this->condition,
        ];
        $resources = json_decode(json_encode($this->resource), true);
        if (MtgJsonUtil::isNotEmpty(Con::PRICE, $resources)) {
            $array[Con::PRICE] = $this->price;
        }
        if (MtgJsonUtil::isNotEmpty(Header::QUANTITY, $resources)) {
            $array[Header::QUANTITY] = $this->quantity;
        }

        $array = CardInfoJsonUtil::setFoilInfo($array, $this->isFoil, $this->foiltype);
        return $array;
    }
}
