<?php

namespace App\Http\Resources\Notion;

use App\Enum\CardLanguage;
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
                'barcode' => $this->getBarcode(),
                'name' => $this->getName(),
                'enname' => $this->getEnname(),
                'number' => $this->getIndex(),
                'color'=>$this->getColor(),
                'price' => $this->getPrice(),
                'stock' => $this->getStock(),
                'image_url' => $this->getImageUrl(),
                'foil' => ['is_foil' => $this->isFoil(),'name' => $this->isFoil() ? 'Foil' : ''],
                'lang' => CardLanguage::reverse($this->getLang()),
                'condition' => $this->getCondition(),
                'exp' => ['name' => $set['name'], 'attr' => $set['attr']],
                'desc' => $this->getDesc()
            ];
    }
}
