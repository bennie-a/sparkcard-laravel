<?php

namespace App\Models;

use App\Services\Constant\BaseApiConstant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaseToken extends Model
{
    use HasFactory;

    protected $table = 'base_token';

    protected $fillable = [
        'access_token',
        'refresh_token',
        'expires_in',
    ];

    // 暗号化
    protected $casts = [
        'access_token' => 'encrypted',
        'refresh_token' => 'encrypted',
        'expires_in' => 'datetime',
    ];

    /**
     * 有効期限が最新のレコードを1件取得する。
     *
     * @return BaseToken
     */
    public static function fetchLastRecord() {
        return self::latest(BaseApiConstant::EXPIRES_IN)->first();
    }
}
