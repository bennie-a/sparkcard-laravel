<?php

namespace App\Http\Resources\Arrival;

use App\Http\Resources\CardInfoResource;
use Illuminate\Http\Request;
use App\Services\Constant\StockpileHeader as Header;
use App\Services\Constant\CardConstant as Con;
use App\Services\Constant\GlobalConstant as GCon;
/**
 * 入荷情報をJSON形式で整形するクラス
 */
class ArrivalLogResource extends CardInfoResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $array[GCon::ID] = $this->arrival_id;
        $array[Header::COST] = $this->cost;
        $array[Header::QUANTITY] = $this->alog_quan;
        $array[GCon::CARD] = parent::toArray($request);
        $array[Con::CARD][Header::LANG] = $this->language;
        $array[GCon::CARD][Header::CONDITION] = $this->condition;
        return $array;
    }
}
