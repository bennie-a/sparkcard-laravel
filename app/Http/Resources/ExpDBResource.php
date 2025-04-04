<?php

namespace App\Http\Resources;

use App\Libs\CarbonFormatUtil;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpDBResource extends JsonResource
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
                'name' => $this->name,
                'attr' => $this->attr,
                'release_date' => CarbonFormatUtil::toDateString($this->release_date)
            ];
    }
}
