<?php

namespace App\Http\Resources\Stockpile;

use App\Http\Resources\CardInfoResource;
use App\Libs\CarbonFormatUtil;
use Illuminate\Http\Request;
use App\Services\Constant\StockpileHeader as Header;
use App\Services\Constant\CardConstant as Con;
use App\Services\Constant\GlobalConstant as GCon;

class StockpileResource extends CardInfoResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $array[GCon::ID] = $this->id;
        $array[Header::LANG] = $this->language;
        $array[Header::QUANTITY] = $this->quantity;
        $array[GCon::UPDATED_AT] = CarbonFormatUtil::toDateString($this->updated_at);
        $array[GCon::CARD] = parent::toArray($request);
        return $array;
    }
}
