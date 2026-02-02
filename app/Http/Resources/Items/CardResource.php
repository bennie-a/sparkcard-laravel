<?php

namespace App\Http\Resources\Items;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\Constant\GlobalConstant as GCon;
use App\Services\Constant\CardConstant as CCon;
/**
 * カード情報に関するResourceクラス
 */
class CardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            GCon::ID => $this->id,
            GCon::NAME => $this->name,
            CCon::EXP => [CCon::ATTR => $this->expansion->attr, GCon::NAME => $this->expansion->name],
            CCon::NUMBER => $this->number,
            CCon::COLOR => $this->color_id,
            CCon::IMAGE_URL => $this->image_url,
            CCon::FOIL => [GCon::ID => $this->foiltype->id, GCon::NAME => $this->foiltype->name],
            CCon::PROMOTYPE => [GCon::ID => $this->promotype->id, GCon::NAME => $this->promotype->name]
        ];
    }
}
