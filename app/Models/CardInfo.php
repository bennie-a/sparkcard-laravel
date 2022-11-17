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

    protected $fillable = ['exp_id', 'barcode','name', 'en_name', 'number', 'color_id', 'image_url'];

    /**
     * 検索条件に該当するデータを取得する。
     *
     * @param array $condition
     * @return array 検索結果
     */
    public static function fetchByCondition($condition)
    {
        $columns = ['card_info.id', 'card_info.number', 'card_info.name','card_info.en_name','card_info.color_id','card_info.image_url'];
        $cardList = self::select($columns)->where($condition)->
                        join('expansion', 'expansion.notion_id', '=', 'card_info.exp_id')->get();
        return $cardList;
    } 
}
