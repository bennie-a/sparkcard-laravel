<?php

namespace App\Http\Resources\Notion;

use Illuminate\Http\Resources\Json\JsonResource;

class NotionCardResource extends JsonResource
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
                'id'=>$this->getId(),
                'index' => $this->getIndex(),
                'name' => $this->getName(),
                'color'=>$this->getColor(),
                'price' => $this->getPrice(),
                'stock' => $this->getStock(),
                // 'expansion' => $this->getExpansion()
            ];
    }
}