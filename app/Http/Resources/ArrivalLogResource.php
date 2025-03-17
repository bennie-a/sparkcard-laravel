<?php
namespace App\Http\Resources;

use App\Libs\CarbonFormatUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\Constant\StockpileHeader as Header;

class ArrivalLogResource extends JsonResource {

    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'attr' => $this->attr,
            'cardname' => $this->cardname,
            Header::LANG => $this->lang,
            Header::ARRIVAL_DATE => CarbonFormatUtil::toDateString($this->arrival_date),
            'sum_cost' => $this->sum_cost,
            'item_count' => $this->item_count,
            Header::VENDOR => [
                Header::VENDOR_TYPE_ID => $this->vendor_type_id,
                Header::NAME => $this->vendor
            ],
            'foil' => [
                'is_foil' => $this->is_foil,
                'name' => $this->foiltag == '通常版' ? '' :$this->foiltag
            ]
        ];
    }
}