<?php
namespace App\Http\Resources;

use App\Libs\CarbonFormatUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\Constant\StockpileHeader as Header;
use App\Services\Constant\ArrivalConstant as ACon;
use App\Services\Constant\SearchConstant as SCon;
use App\Services\Constant\CardConstant as Con;
use App\Libs\CardInfoJsonUtil;

class ArrivalGroupingLogResource extends CardInfoResource {

    public function toArray(Request $request)
    {
        $array =  [
            Con::ID => $this->arrival_id,
            ACon::ARRIVAL_DATE => CarbonFormatUtil::toDateString($this->arrival_date),
            'sum_cost' => $this->sum_cost,
            'item_count' => $this->item_count,
            ACon::VENDOR => [
                SCon::VENDOR_TYPE_ID => $this->vendor_type_id,
                Header::NAME => $this->vcat,
                Header::SUPPLIER => $this->vendor
            ],
        ];
        $array[Con::CARD] = parent::toArray($request);
        return $array;
    }
}