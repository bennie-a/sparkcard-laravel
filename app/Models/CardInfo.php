<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardInfo extends Model
{
    use HasFactory;
    protected $table = 'card_info';

    public function expansion()
    {
        return $this->belongsTo('App\Models\Expansion');
    }

    protected $fillable = ['expansion.name', 'expansion.attr',  'exp_id', 'barcode','name', 'en_name', 'number', 'color_id', 'image_url', 'isFoil', 'language'];

    /**
     * 検索条件に該当するデータを取得する。
     *
     * @param array $condition
     * @return array 検索結果
     */
    public static function fetchByCondition($condition)
    {
        $columns = ['expansion.name as exp_name', 'expansion.attr as exp_attr', 'card_info.id', 'card_info.number', 'card_info.name','card_info.en_name','card_info.color_id','card_info.image_url', 'card_info.isFoil'];
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
        $cardList = $query->join('expansion', 'expansion.notion_id', '=', 'card_info.exp_id')->
                        orderBy('card_info.number', 'asc')->get();
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
}
