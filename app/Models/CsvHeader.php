<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CsvHeader extends Model
{
    use HasFactory;

    protected $table = 'csv_header';

    protected $fillable = ['id', 'shop',  'column'];

    protected $visible = ['id', 'column'];

    /**
     * 'shop'カラムと合致するレコードを取得する。
     *
     * @param string $shop
     * @return array
     */
    public static function findColumns(string $shop) {
       $result = self::select('column')->where('shop', '=', $shop)->orderBy('order_id')->get();
        $header = array_map(function($c)  {
            return $c['column'];
        }, $result->toArray());
       return $header;
    }
}