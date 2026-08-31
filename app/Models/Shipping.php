<?php

namespace App\Models;

use App\Models\Shipt\Orders;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\Constant\CardConstant as Con;
use App\Services\Constant\GlobalConstant as GC;
/**
 * 発送方法テーブルのModelクラス
 */
class Shipping extends Model
{
    use HasFactory;
    protected $table = 'shipping';

    protected $fillable = [GC::ID, 'notion_id', GC::NAME, Con::PRICE];

    public static function findByNotionId(string $notionId) {
        $item = self::where('notion_id', $notionId)->first();
        return $item;
    }

    /**
     * 発送名に一致するデータを取得する。
     *
     * @param string $method
     * @return Shipping
     */
    public static function findByMethod(string $method) {
        return self::where(GC::NAME, $method)->first();
    }

    public function orders() {
        return $this->hasMany(Orders::class, 'shipt_fee_id');
    }
}
