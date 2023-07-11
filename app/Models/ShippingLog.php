<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
/**
 * shippinng_logテーブルのModelクラス
 */
class ShippingLog extends Model
{
    protected $table = 'shipping_log';

    protected $fillable = ['name', 'stock_id',  'zip_code', 'address', 'quantity', 'shipping_date', 'single_price', 'total_price', 'order_id'];


    use HasFactory;
}
