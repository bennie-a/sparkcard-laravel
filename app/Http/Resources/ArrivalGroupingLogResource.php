<?php
namespace App\Http\Resources;

use App\Libs\CarbonFormatUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\Constant\StockpileHeader as Header;
use App\Services\Constant\ArrivalConstant as Con;
use App\Services\Constant\SearchConstant as SCon;
use App\Libs\CardInfoJsonUtil;

class ArrivalGroupingLogResource extends JsonResource {

    public function toArray(Request $request)
    {
        $array =  [
            Con::ID => $this->id,
            Con::ATTR => $this->attr,
            Header::NAME => $this->cardname,
            Header::LANG => $this->lang,
            Con::ARRIVAL_DATE => CarbonFormatUtil::toDateString($this->arrival_date),
            'sum_cost' => $this->sum_cost,
            'item_count' => $this->item_count,
            Con::VENDOR => [
                SCon::VENDOR_TYPE_ID => $this->vendor_type_id,
                Header::NAME => $this->vcat,
                Header::SUPPLIER => $this->vendor
            ],
        ];
        $array = CardInfoJsonUtil::setFoilInfo($array, $this->is_foil, $this->foiltag);
        return $array;
    }
}