<?php

namespace App\Http\Resources;

use App\Libs\CarbonFormatUtil;
use App\Services\Constant\CardConstant;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\Constant\StockpileHeader as Header;
use App\Services\Constant\ArrivalConstant as Con;

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
        $array[Con::ID] = $this->id; 
        $array[Con::ARRIVAL_DATE] = CarbonFormatUtil::toDateString($this->arrival_date);
        $array[Con::VENDOR] = [
            Con::ID => $this->vendor_type_id,
            Con::NAME => $this->vcat,
            Header::SUPPLIER => $this->vendor
        ];
        $array[Header::COST] = $this->cost;
        $array[Con::CARD] = parent::toArray($request);
        return $array;
    }
}
