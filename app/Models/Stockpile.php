<?php

namespace App\Models;

use App\Libs\MtgJsonUtil;
use App\Services\Constant\SearchConstant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\Constant\StockpileHeader as Header;
use Carbon\Carbon;

/**
 * stockpileテーブルのModelクラス
 */
class Stockpile extends Model
{
    use HasFactory;

    protected $table = 'stockpile';

    protected $fillable = ['card_id', 'language',  'condition', 'quantity', 'updated_at'];

    public function cardinfo() {
        return $this->belongsTo('App\Models\CardInfo');
    }

    public static function find(string $cardname, string $condition, string $language, bool $isFoil)  {
        $columns = ['s.id', 'c.name as cardname', 's.card_id as card_id', 's.condition', 's.quantity', 'c.isFoil as isFoil', 's.language'];
        $query = self::select($columns)->from('stockpile as s');
        $query = $query->join('card_info as c', 's.card_id',  '=', 'c.id');
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
     * @return Stockpile
     */
    public static function findSpecificCard(int $cardId, string $language, string $condition):Stockpile {
        $stockpile = Stockpile::where(['card_id' => $cardId, 'language' => $language, 'condition' => $condition])->first();
        return $stockpile ?? new Stockpile();
    }

    /**
     * カード名とセット名に部分一致する在庫情報を取得する。
     *
     * @param array $details
     * @return array
     */
    public static function fetch(array $details) {
        $columns = ['s.id', 'e.name as setname', 'c.name as cardname', 's.language', 's.condition', 's.quantity', 'c.image_url', 'c.isFoil as isFoil', 's.updated_at as updated_at'];
        $query = self::from('stockpile as s')->select($columns);
        $query = $query->join('card_info as c', 's.card_id',  '=', 'c.id')->join('expansion as e', 'c.exp_id', '=', 'e.notion_id');
        $cardname = MtgJsonUtil::hasKey(SearchConstant::CARD_NAME, $details) ? $details[SearchConstant::CARD_NAME] : '';
        if (!empty($cardname)) {
            $query = $query->where('c.name', 'like', '%'.$cardname.'%');
        }
        $setname = MtgJsonUtil::hasKey(SearchConstant::SET_NAME, $details) ? $details[SearchConstant::SET_NAME] : '';
        if (!empty($setname)) {
            $query = $query->where('e.name', 'like', '%'.$setname.'%');
        }
        $limit = $details[SearchConstant::LIMIT];
        if (!empty($limit) && $limit > 0) {
            $query = $query->limit($limit);
        }
        return $query->orderBy('s.id', 'asc')->get();
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format("Y/m/d");
    }

}
