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
        $set = $this->getExpansion();
        return [
                'id'=>$this->getId(),
                'index' => $this->getIndex(),
                'barcode' => $this->getBarcode(),
                'name' => $this->getName(),
                'enname' => $this->getEnname(),
                'color'=>$this->getColor(),
                'price' => $this->getPrice(),
                'stock' => $this->getStock(),
                'image' => $this->getImageUrl(),
                'isFoil' => $this->isFoil(),
                'lang' => $this->getLang(),
                'condition' => $this->getCondition(),
                'exp' => ['name' => $set['name'], 'attr' => $set['attr']],
                'desc' => $this->getDesc()
            ];
    }
}
