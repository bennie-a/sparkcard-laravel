<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Promotype extends Model
{
    use HasFactory;

    protected $table = 'promotype';

    protected $fillable = ['id', 'attr','name'];

    /**
     * 略称から該当するプロモ情報を取得する。
     *
     * @param string $attr
     * @return Promotype
     */
    public static function findCardByAttr(string $attr) {
        return Promotype::where('attr', $attr)->first();
    }

}
