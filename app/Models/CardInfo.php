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
        return $this->belongsTo('App\Expansion');
    }

    protected $fillable = ['exp_id', 'barcode','name', 'en_name', 'number', 'color_id', 'image_url', 'isFoil'];

    /**
     * 検索条件に該当するデータを取得する。
     *
     * @param array $condition
     * @return array 検索結果
     */
    public static function fetchByCondition($condition)
    {
        $columns = ['card_info.id', 'card_info.number', 'card_info.name','card_info.en_name','card_info.color_id','card_info.image_url', 'card_info.isFoil'];
        $cardList = self::select($columns)->where($condition)->
                        join('expansion', 'expansion.notion_id', '=', 'card_info.exp_id')->
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
    public static function findCard($exp_id, $name) {
        $columns = ['card_info.name', 'card_info.barcode', 'card_info.number'];
        $info = self::select($columns)->where(['exp_id' => $exp_id, 'name' => $name])->first();
        return $info;
    }

    public static function findCardByAttr($attr, $name) {
        $condition = ['expansion.attr' => $attr, 'card_info.name' => $name];
        $list = self::fetchByCondition($condition);
        return $list[0];
    }
}
