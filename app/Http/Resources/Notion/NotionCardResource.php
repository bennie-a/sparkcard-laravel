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
        if (is_null($this->getImageUrl())) {
            $this->setImageUrl("none");
        }
        return [
                'id'=>$this->getId(),
                'index' => $this->getIndex(),
                'name' => $this->getName(),
                // 'enname' => $this->getEnname(),
                'color'=>$this->getColor(),
                'price' => $this->getPrice(),
                'stock' => $this->getStock(),
                'image' => $this->getImageUrl(),
                'isFoil' => $this->isFoil(),
                'lang' => $this->getLang()
            ];
    }
}
