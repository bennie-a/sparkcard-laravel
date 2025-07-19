<?php

namespace App\Models;

use App\Libs\CarbonFormatUtil;
use App\Libs\MtgJsonUtil;
use App\Services\Constant\GlobalConstant;
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

    protected $fillable = ['id', 'card_id', 'language',  'condition', 'quantity', 'updated_at'];

    public function cardinfo() {
        return $this->belongsTo('App\Models\CardInfo');
    }

    public static function find(int  $card_id, string $setcode, string $condition, string $language, bool $isFoil)  {
        $columns = ['s.id', 'c.name as cardname', 's.card_id as card_id', 's.condition', 's.quantity', 'c.isFoil as isFoil', 's.language'];
        $query = self::select($columns)->from('stockpile as s');
        $query = $query->join('card_info as c', 's.card_id',  '=', 'c.id')->join('expansion as e', 'c.exp_id', '=', 'e.notion_id');
        $stock = $query->where(['c.id' => $card_id, 'c.isFoil' => $isFoil,
                                             's.condition' => $condition, 's.language' => $language, 'e.attr' => $setcode])->first();
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
        $columns = ['s.id as stock_id', 'e.name as exp_name', 'e.attr as exp_attr', 'c.id', 'c.name as name', 
                                'c.color_id', 's.language', 's.condition', 's.quantity',
                                'c.image_url', 'c.number', 'c.isFoil as isFoil', 'f.name as foiltype',
                                'c.promotype_id', 'p.name as promo_name', 's.updated_at as updated_at'];
        $query = self::from('stockpile as s')->select($columns);
        $query = $query->join('card_info as c', 's.card_id',  '=', 'c.id')
                                    ->join('expansion as e', 'c.exp_id', '=', 'e.notion_id')
                                    ->join('foiltype as f', 'f.id', '=', 'c.foiltype_id')
                                    ->join('promotype as p', 'p.id', '=', 'c.promotype_id');
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
        return $query->orderBy('s.id', 'asc')->orderBy('c.number', 'asc')->get();
    }


    /**
     * 在庫情報を更新する。
     *
     * @param array $details
     * @return void
     */
    public static function updateData(int $stock_id, array $data) {
        $target = self::findById($stock_id);
        if (empty($target)) {
            return;
        }
        $target->update($data);
    }

    public function getUpdatedAtAttribute($value)
    {
        return CarbonFormatUtil::toDateString($value);
    }

    public static function findById(int $id): ?Stockpile
    {
        return self::where(GlobalConstant::ID, $id)->first();
    }
}
