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

    public static function fetchByOrderId(string $orderId) {
        $columns = ['slog.order_id as order_id', 'slog.quantity as quantity',
                                    'slog.name as name', 'slog.zip_code as zip',
                                     'slog.address as address', 'slog.shipping_date as sdate',
                                    'slog.single_price', 'c.name as cardname', 'e.name as setname',
                                     'c.image_url', 's.language as lang', 's.condition as condition'];
        $query = self::from("shipping_log as slog")->select($columns);
        return $query->join("stockpile as s", "s.id","=", "slog.stock_id")
                                    ->join("card_info as c", "c.id", "=", "s.card_id")
                                    ->join("expansion as e", 'e.notion_id', '=', 'c.exp_id')
                                                ->where("slog.order_id", $orderId)->first();

    }
}
