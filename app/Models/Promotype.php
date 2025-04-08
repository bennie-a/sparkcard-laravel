<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Promotype extends Model
{
    use HasFactory;

    protected $table = 'promotype';

    protected $fillable = ['id', 'attr','name'];

    /**
     * 略称から該当するプロモ情報を取得する。
     *
     * @param string $attr
     * @return Promotype
     */
    public static function findCardByAttr(string $attr) {
        return Promotype::where('attr', $attr)->first();
    }

    /**
     * 名称から該当するプロモ情報を取得する。
     *
     * @param string $name
     * @return Promotype
     */
    public static function findCardByName(string $name) {
        return Promotype::where('name', $name)->first();
    }

    /**
     * エキスパンション略称に該当する特別版を取得する。
     *
     * @param string $setcode
     * @return Collection
     */
    public static function findBySetCode(string $setcode) {
        $condition = ['COM', $setcode];
        $columns = ['p.id', 'p.attr', 'p.name', 'e.attr as setcode'];
        $query = DB::table('promotype as p')->select($columns);
        $result = $query->join('expansion as e', function($join) {
                                    $join->on('p.exp_id', 'e.notion_id');
                                })->whereIn('e.attr', $condition)
                                ->orderBy('e.release_date')->orderBy('p.id')->get();
        return $result;
    }
}
