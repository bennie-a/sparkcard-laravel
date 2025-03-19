<?php

namespace App\Http\Resources;

use App\Enum\CardColor;
use App\Libs\CardInfoJsonUtil;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\Constant\StockpileHeader as Header;
use App\Services\Constant\CardConstant as Con;
class CardInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $array =  [
            Con::ID => $this->id,
            Con::EXP => [Con::NAME => $this->exp_name, Con::ATTR => $this->exp_attr],
            'number' => $this->number,
            'name' => $this->name,
            'enname' => $this->en_name,
            'price' => $this->price,
            'color' => CardColor::tryFrom($this->color_id)->text(),
            'image' => $this->image_url,
            'condition' => $this->condition,
            'quantity' => $this->quantity
        ];
        $array = CardInfoJsonUtil::setFoilInfo($array, $this->isFoil, $this->foiltype);
        return $array;
    }
}
