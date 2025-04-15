<?php

namespace App\Models;

use App\Libs\MtgJsonUtil;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\Constant\StockpileHeader as Header;
use Carbon\Carbon;

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
                                    'slog.single_price', 'slog.total_price', 's.id as stock_id', 'c.name as cardname', 'e.name as setname','e.attr as exp_attr',
                                     'c.image_url as image_url', 'c.number', 's.language as lang', 's.condition as condition', 'c.isFoil as isFoil', 'f.name as foilname'];
        $query = self::from("shipping_log as slog")->select($columns);
        return $query->join("stockpile as s", "s.id","=", "slog.stock_id")
                                    ->join("card_info as c", "c.id", "=", "s.card_id")
                                    ->join("expansion as e", 'e.notion_id', '=', 'c.exp_id')
                                    ->join("foiltype as f", "f.id", '=', 'c.foiltype_id')
                                                ->where("slog.order_id", $orderId)->get();
    }

    public static function fetch($details) {
        $buyer = $details[Header::BUYER];
        $shiptDate = null;
        if (MtgJsonUtil::hasKey(Header::SHIPPING_DATE, $details)) {
            $shiptDate = $details[Header::SHIPPING_DATE];
        }
        $query = ShippingLog::select('order_id', 'name', 'zip_code', 'address', 'shipping_date')->
        selectRaw('count(order_id) as item_count, sum(total_price) as total_price');
        if ($buyer != null) {
            $pat = '%' . addcslashes($buyer, '%_\\') . '%';
            $query = $query->where('name', 'LIKE', $pat);
        }
        if ($shiptDate != null) {
            $query = $query->whereDate(Header::SHIPPING_DATE, $shiptDate);
        }
        return $query->orderBy('shipping_date', 'desc')
            ->groupby('order_id', 'name', 'zip_code', 'address', 'shipping_date')->get();
    }

    public function getShippingDateAttribute($value) {
        return Carbon::parse($value)->format("Y/m/d");
    }

    /**
     * 注文IDと購入者名、在庫IDを条件にデータが存在するかチェックする。
     *
     * @param string $orderId
     * @param string $buyer
     * @param integer $stockId
     * @return bool
     */
    public static function isExists(string $orderId, string $buyer, int $stockId):bool {
        $condition = [
            ["order_id", "=", $orderId],
            [ "name", "=", $buyer],
            ["stock_id", "=", $stockId]
        ];
        return ShippingLog::where($condition)->exists();
    }
}
