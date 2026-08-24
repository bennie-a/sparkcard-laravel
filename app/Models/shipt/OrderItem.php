<?php

namespace App\Models\Shipt;

use App\Models\Stockpile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    /** @use HasFactory<\Database\Factories\Shipt\OrderItemFactory> */
    use HasFactory;

    protected $table = 'order_items';

    protected $fillable = ['id', 'order_id', 'stock_id',  'quantity', 'unit_price', 'subtotal'];

    public function order()
    {
        return $this->belongsTo(Orders::class, 'order_id');
    }

    public function stockpile()
    {
        return $this->belongsTo(Stockpile::class, 'stock_id');
    }
}
