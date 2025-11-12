<?php

namespace App\Http\Resources\Shipt;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Services\Constant\ShiptConstant as SC;

/**
 * 1件分の注文情報をまとめるResourceクラス。
 * @since 5.1.0
 */
class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            SC::ORDER_ID => $this[SC::ORDER_ID],
        ];
    }
}
