<?php

namespace App\Http\Resources;

use App\Libs\CarbonFormatUtil;
use App\Services\Constant\CardConstant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\Constant\StockpileHeader as Header;
use App\Services\Constant\CardConstant as Con;
use App\Services\Constant\ArrivalConstant as ACon;

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
        $array[Con::ID] = $this->arrival_id; 
        $array[ACon::ARRIVAL_DATE] = CarbonFormatUtil::toDateString($this->arrival_date);
        $array[ACon::VENDOR] = [
            Con::ID => $this->vendor_type_id,
            Con::NAME => $this->vcat,
            Header::SUPPLIER => $this->vendor
        ];
        $array[Header::COST] = $this->cost;
        $array[Header::QUANTITY] = $this->alog_quan;
        $array[Con::CARD] = parent::toArray($request);
        $array[Con::CARD][Header::LANG] = $this->language;
        $array[Con::CARD][Header::CONDITION] = $this->condition;
        return $array;
    }
}
