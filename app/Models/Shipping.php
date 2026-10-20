<?php

namespace App\Models;

use App\Models\Shipt\Orders;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\Constant\CardConstant as Con;
use App\Services\Constant\GlobalConstant as GC;
use Illuminate\Database\Eloquent\Builder;

/**
 * 発送方法テーブルのModelクラス
 */
class Shipping extends Model
{
    use HasFactory;
    protected $table = 'shipping';

    protected $fillable = [GC::ID, 'notion_id', GC::NAME, Con::PRICE, 'deleted'];

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
        return self::where(GC::NAME, $method)->where('deleted', false)->first();
    }

    public function orders() {
        return $this->hasMany(Orders::class, 'shipt_fee_id');
    }

    public function scopeActive(Builder $query):Builder
    {
        return $query->where('deleted', false);
    }
}
