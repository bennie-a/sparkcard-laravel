<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
/**
 * foiltypeテーブルのModelクラス
 */
class Foiltype extends Model
{
    use HasFactory;

    protected $table = 'foiltype';

    protected $fillable = ['id', 'attr',  'name'];

    /**
     * Foil名を条件に検索する。
     *
     * @param string $name
     * @return Foiltype
     */
    public static function findByName(string $name) {
        return self::where('name', '=', $name)->first();
    }
}
