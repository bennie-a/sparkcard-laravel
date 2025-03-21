<?php

namespace App\Http\Resources;

use App\Libs\CarbonFormatUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\Constant\StockpileHeader as Header;

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
        $array = parent::toArray($request);
        $array[Header::ARRIVAL_DATE] = CarbonFormatUtil::toDateString($this->arrival_date);
        $array[Header::VENDOR] = [
                                        Header::VENDOR_TYPE_ID => $this->vendor_type_id,
                                        Header::NAME => $this->vendor
                                    ];
        return $array;
    }
}
