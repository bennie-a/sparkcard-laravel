<?php

namespace App\Models\Shipt;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    /** @use HasFactory<\Database\Factories\Shipt\OrdersFactory> */
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = ['id', 'platform', 'platform_order_id',  'buyer_name', 'zip_code', 'address', 'item_count',
                                            'items_subtotal ', 'coupon_discount', 'shipt_fee_id', 'grand_total', 'shipt_date'];

    public function orderitems()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }
}
