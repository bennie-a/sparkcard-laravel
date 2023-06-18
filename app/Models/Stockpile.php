<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stockpile extends Model
{
    use HasFactory;

    protected $table = 'stockpile';

    protected $fillable = ['card_id', 'language',  'condition', 'quantity'];

    /**
     * 在庫情報が存在するか判定する。
     */
    public static function isExists(int $cardId, string $lang, string $condition) {
        return Stockpile::where(['card_id' => $cardId, 'language' => $lang, 'condition' => $condition])->exists();
    }
}
