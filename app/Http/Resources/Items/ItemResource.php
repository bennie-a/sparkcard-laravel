<?php

namespace App\Http\Resources\Items;

use App\Services\Constant\GlobalConstant as GCon;
use App\Services\Constant\StockpileHeader as SH;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * 在庫情報に関するResourceクラス
 */
class ItemResource extends JsonResource
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
            GCon::CARD => new CardResource($this->cardInfo),
            SH::LANG => $this->lang,
            SH::CONDITION => $this->condition,
            SH::QUANTITY => $this->quantity,
        ];
    }
}
