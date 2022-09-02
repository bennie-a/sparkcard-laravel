<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WisdomGuildResource extends JsonResource
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
                'data' => [
                    'index' => $this->getIndex(),
                    'name' => $this->getName(),
                    'enname' => $this->getEnname(),
                    'price' => $this->getPrice()
                ]
            ];
    }
}
