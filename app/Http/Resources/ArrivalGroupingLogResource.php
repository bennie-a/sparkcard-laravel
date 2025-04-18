<?php
namespace App\Http\Resources;

use App\Libs\CarbonFormatUtil;
use Illuminate\Http\Request;
use App\Services\Constant\StockpileHeader as Header;
use App\Services\Constant\ArrivalConstant as ACon;
use App\Services\Constant\CardConstant as Con;
use App\Services\Constant\GlobalConstant as GCon;


class ArrivalGroupingLogResource extends CardInfoResource {

    public function toArray(Request $request)
    {
        $array =  [
            GCon::ID => $this->arrival_id,
            ACon::ARRIVAL_DATE => CarbonFormatUtil::toDateString($this->arrival_date),
            'sum_cost' => $this->sum_cost,
            'item_count' => $this->item_count,
            ACon::VENDOR => [
                GCon::ID => $this->vendor_type_id,
                Header::NAME => $this->vcat,
                ACon::SUPPLIER => $this->vendor
            ],
        ];
        $array[Con::CARD] = parent::toArray($request);
        $array[Con::CARD][Header::LANG] = $this->lang;
        return $array;
    }
}