<?php

namespace App\Models;

use App\Services\Constant\SearchConstant;
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

    /**
     * カード名とセット名に部分一致する在庫情報を取得する。
     *
     * @param array $details
     * @return array
     */
    public static function fetch(array $details) {
        $columns = ['s.id', 'e.name as setname', 'c.name as cardname', 's.language', 's.condition', 's.quantity', 'c.image_url', 'c.isFoil as isFoil'];
        $query = self::select($columns)->from('stockpile as s');
        $query = $query->join('card_info as c', 's.card_id',  '=', 'c.id')->join('expansion as e', 'c.exp_id', '=', 'e.notion_id');
        $cardname = $details[SearchConstant::CARD_NAME];
        if (!empty($cardname)) {
            $query = $query->where('c.name', 'like', '%'.$cardname.'%');
        }
        $setname = $details[SearchConstant::SET_NAME];
        if (!empty($setname)) {
            $query = $query->where('e.setname', 'like', '%'.$setname.'%');
        }
        $limit = $details[SearchConstant::LIMIT];
        if ($limit > 0) {
            $query = $query->limit($limit);
        }
        return $query->orderBy('c.id', 'asc')->get();
    }

}
