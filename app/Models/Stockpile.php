<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\Constant\StockpileHeader as Header;

/**
 * stockpileテーブルのModelクラス
 */
class Stockpile extends Model
{
    use HasFactory;

    protected $table = 'stockpile';

    protected $fillable = ['card_id', 'language',  'condition', 'quantity'];

    public function cardinfo() {
        return $this->belongsTo('App\Models\CardInfo');
    }

    public static function find(string $cardname, string $condition, string $language, bool $isFoil)  {
        $columns = ['s.id', 'c.name as cardname', 's.card_id as card_id', 's.condition', 's.quantity', 'c.isFoil as isFoil', 's.language'];
        $query = self::select($columns)->from('stockpile as s');
        $query = $query->join('card_info as c', 's.card_id',  '=', 'c.id');
        // $stock = $query->where([['cinfo.name', '=',  $cardname], ['sinfo.isFoil', '=', $isFoil],  ['s.condition', '=', $condition],
        //                  ['s.language', '=', $language]])->first();
        $stock = $query->where(['c.name' => $cardname, 'c.isFoil' => $isFoil,
                                             's.condition' => $condition, 's.language' => $language])->first();
        return $stock;
    }

    /**
     * 在庫情報が存在するか判定する。
     */
    public static function isExists(int $cardId, string $lang, string $condition) {
        return Stockpile::where(['card_id' => $cardId, 'language' => $lang, 'condition' => $condition])->exists();
    }

    /**
     * カード情報IDと言語、状態から特定の在庫情報を取得する。
     *
     * @param integer $cardId
     * @param string $language
     * @param string $condition
     * @return void
     */
    public static function findSpecificCard(int $cardId, string $language, string $condition) {
        return Stockpile::where(['card_id' => $cardId, 'language' => $language, 'condition' => $condition])->first();
    }

}
