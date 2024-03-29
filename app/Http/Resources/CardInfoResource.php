<?php

namespace App\Http\Resources;

use App\Enum\CardColor;
use Illuminate\Http\Resources\Json\JsonResource;

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
        return [
                'id' => $this->id,
                'exp' => ['name' => $this->exp_name, 'attr' => $this->exp_attr],
                'number' => $this->number,
                'name' => $this->name,
                'enname' => $this->en_name,
                'price' => $this->price,
                'color' => CardColor::tryFrom($this->color_id)->text(),
                'image' => $this->image_url,
                'isFoil' => $this->isFoil,
                'foiltype' => $this->foiltype,
                'condition' => $this->condition,
                'quantity' => $this->quantity
            ];
    }
}
