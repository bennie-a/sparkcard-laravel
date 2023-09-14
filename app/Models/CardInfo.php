<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Services\Constant\CardConstant as Con;

class CardInfo extends Model
{
    use HasFactory;
    protected $table = 'card_info';

    public function expansion()
    {
        return $this->belongsTo('App\Models\Expansion');
    }

    public function stockpiles() {
        return $this->hasMany('App\Models\Stockpile');
    }

    public function foiltype() {
        return $this->hasOne('App\Model\Foiltype');
    }

    protected $fillable = ['id', 'expansion.name', 'expansion.attr',  'exp_id', 'barcode','name', 'en_name', 'number', 'color_id', 'image_url', 'isFoil', 'foiltype_id'];

    /**
     * 検索条件に該当するデータを取得する。
     *
     * @param array $condition
     * @return array 検索結果
     */
    public static function fetchByCondition($condition)
    {
        $columns = ['e.name as exp_name', 'e.attr as exp_attr', 'card_info.id', 'card_info.number',
                 'card_info.name','card_info.en_name','card_info.color_id','card_info.image_url', 
                 'card_info.isFoil', 'f.name as foiltype', 's.condition', 's.quantity'];
        $name = $condition['card_info.name'];
        $query = self::select($columns);
        if (!empty($name)) {
            $query = $query->where('card_info.name', 'like', '%'.$name.'%');
        }
        foreach($condition as $key => $value) {
            if (strcmp('card_info.name', $key) == 0) {
                continue;
            }
            if (!empty($value)) {
                $query = $query->where($key, $value);
            }
        }
        $cardList = $query->join('expansion as e', 'e.notion_id', '=', 'card_info.exp_id'
                                )->leftJoin('stockpile as s', 'card_info.id', '=', 's.card_id')->join('foiltype as f', 'f.id', '=', 'card_info.foiltype_id')->
                        orderBy('e.release_date', 'desc')->orderByRaw('CAST(card_info.number as integer ) asc')->get();
        return $cardList;
    }

    /**
     * エキスパンションIDとカード名から特定のカード情報を
     * 取得する。
     *
     * @param string $exp_id エキスパンションID
     * @param [type] $name カード名
     * @return カード情報
     */
    public static function findCard($exp_id, $name, $isFoil) {
        $columns = ['card_info.name', 'card_info.barcode', 'card_info.number', 'card_info.image_url'];
        $info = self::select($columns)->where(['exp_id' => $exp_id, 'name' => $name, 'isFoil' => $isFoil])->first();
        return $info;
    }

    public static function findCardByAttr($attr, $name) {
        $condition = ['expansion.attr' => $attr, 'card_info.name' => $name];
        $list = self::fetchByCondition($condition);
        return $list[0];
    }

    public static function findSingleCard($setcode, $name, $isFoil) {
        return self::findSingleQuery($setcode, $name, $isFoil)->first();
    }

    public static function isExist($setcode, $name, $isFoil):bool {
        $query = self::findSingleQuery($setcode, $name, $isFoil);
        return $query->exists();
    }

    private static function findSingleQuery($setcode, $name, $isFoil) {
        $conditions = ['expansion.attr' => $setcode, 'card_info.name' => $name, 'card_info.isFoil' => $isFoil];
        return  CardInfo::where($conditions)->join('expansion', 'expansion.notion_id', '=', 'card_info.exp_id');
    }

    /**
     * 特定のカード情報を取得する。
     *
     * @param [type] $exp_id
     * @param [type] $name
     * @param [type] $isFoil
     * @return カード情報
     */
    public static function findSpecificCard($exp_id, $name, $foiltype_id)
    {
        $columns = ['card_info.name', 'card_info.barcode', 'card_info.number'];
        $info = self::select($columns)->where(['exp_id' => $exp_id, 'name' => $name, 'foiltype_id' => $foiltype_id])->first();
        return $info;
    }

    public static function findEnCard($name) {
        $info = self::select('en_name')->where(['name' => $name])->first();
        return $info;
    }

    public static function findJpName(string $enname) {
        $name = self::select('name')->where(['en_name' => $enname])->first();
        return $name;
    }

    public function getQuantityAttribute($value) {
        if (empty($value)) {
            return 0;
        }
        return $value;
    }
}
