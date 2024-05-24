<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\Constant\StockpileHeader as Header;

/**
 * shippinng_logテーブルのModelクラス
 */
class ShippingLog extends Model
{
    protected $table = 'shipping_log';

    protected $fillable = ['name', 'stock_id',  'zip_code', 'address', 'quantity', 'shipping_date', 'single_price', 'total_price', 'order_id'];

    use HasFactory;

    /**
     * 注文番号に該当する出荷情報を取得する。
     *
     * @param string $orderId
     * @return Collection
     */
    public static function fetchByOrderId(string $orderId) {
        $columns = ['slog.order_id as order_id', 'slog.quantity as quantity',
                                    "slog.name as buyer_name", 'slog.zip_code as zip',
                                     'slog.address as address', 'slog.shipping_date as shipping_date',
                                    'slog.single_price', 'slog.total_price', 'c.name as cardname', 'e.name as setname',
                                     'c.image_url', 's.language as lang', 's.condition as condition'];
        $query = self::from("shipping_log as slog")->select($columns);
        return $query->join("stockpile as s", "s.id","=", "slog.stock_id")
                                    ->join("card_info as c", "c.id", "=", "s.card_id")
                                    ->join("expansion as e", 'e.notion_id', '=', 'c.exp_id')
                                                ->where("slog.order_id", $orderId)->get();

    }
}
