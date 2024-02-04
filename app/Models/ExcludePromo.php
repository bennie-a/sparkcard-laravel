<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * exclude_promoテーブルのModelクラス
 */
class ExcludePromo extends Model
{
    use HasFactory;

    protected $table = 'exclude_promo';

    protected $fillable = ['id', 'attr','name'];

    /**
     * パラメータの略称に該当するか判別する。
     *
     * @param array $attrs
     * @return boolean trueなら存在する
     */
    public static function existsByAttr(array $attrs) {
        $query = self::whereIn('attr', $attrs);
        // $query->dd();
        $isExists = $query->exists();
        return $isExists;
    }
}
