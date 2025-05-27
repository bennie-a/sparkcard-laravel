<?php

namespace App\Http\Resources\Arrival;

use App\Libs\CarbonFormatUtil;
use Illuminate\Http\Request;
use App\Services\Constant\ArrivalConstant as ACon;
use App\Libs\VendorJsonUtil;

class ArrivalLogFindResource extends ArrivalLogResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $array[Acon::ARRIVAL_DATE] = CarbonFormatUtil::toDateString($this->arrival_date);
        $json = array_merge($array, parent::toArray($request));
        $json[Acon::VENDOR] = VendorJsonUtil::setVendorInfo(
            $this->vendor_type_id,
            $this->vcat,
            $this->vendor
        );
        return $json;
    }
}
