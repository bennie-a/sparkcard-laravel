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
            Con::EXP => [Con::NAME => $this->exp_name, Con::ATTR => $this->exp_attr],
            'number' => $this->number,
            'name' => $this->name,
            'color' => CardColor::tryFrom($this->color_id)->text(),
            'image' => $this->image_url,
            'condition' => $this->condition,
            'quantity' => $this->quantity
        ];
        $resources = json_decode(json_encode($this->resource), true);
        if (MtgJsonUtil::isNotEmpty(Con::PRICE, $resources)) {
            $array[Con::PRICE] = $this->price;
        }
        $array = CardInfoJsonUtil::setFoilInfo($array, $this->isFoil, $this->foiltype);
        return $array;
    }
}
