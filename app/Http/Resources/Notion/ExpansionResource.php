<?php

namespace App\Http\Resources\Notion;

use Illuminate\Http\Resources\Json\JsonResource;

class ExpansionResource extends JsonResource
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
                'id'=>$this->getNotionId(),
                'name' => $this->getName(),
                'attr' => $this->getAttr()
            ];
    }
}
