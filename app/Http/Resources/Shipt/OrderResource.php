<?php

namespace App\Http\Resources\Shipt;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'id' => $this->id,
            'platform' => $this->platform,
            'platform_order_id' => $this->platform_order_id,
            // 'buyer' => $order->buyer(),
            // 'postal_code' => $order->postal_code(),
            // 'shipping_date' => $order->shipt_date,
            // 'orderitems' => OrderItemResource::collection($this->whenLoaded('orderitems')),
            'prev_id' => $this->previous()?->id ?? 0,
            'next_id' => $this->next()?->id ?? 0,
        ];
        // return parent::toArray($request);
    }
}
