<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Expansion extends Model
{
    use HasFactory;

    protected $table = 'expansion';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['id', 'notion_id', 'name', 'attr', 'release_date'];

    public function cardinfo()
    {
        return $this->hasMany('App\CardInfo');
    }

    public static function isExist($name) {
        return DB::table("expansion")->where('name', $name)->exists();
    }

    public static function isExistByAttr(string $attr) {
        return Expansion::where('attr', $attr)->exists();
    }

    /**
     * 引数のnotion_idに該当するエキスパンションを取得する。
     *
     * @param string $notionId
     * @return Expansion
     */
    public static function findByNotionId(string $notionId) {
        return Expansion::where('notion_id', $notionId)->first();
    }

    public static function findBySetCode(string $setCode) {
        return Expansion::where('attr', $setCode)->first();

    }

}
