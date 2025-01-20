<?php

namespace App\Models;

use App\Libs\MtgJsonUtil;
use Illuminate\Database\Eloquent\Model;
use App\Services\Constant\StockpileHeader as Header;
use Illuminate\Support\Facades\DB;
use App\Services\Constant\SearchConstant as Con;

class ArrivalLog extends Model
{
    protected $table = 'arrival_log';

    protected $fillable = ['id', 'stock_id',  Header::ARRIVAL_DATE, Header::QUANTITY,
                                            Header::COST, Header::VENDOR_TYPE_ID, Header::VENDOR];


    /**
     * カード情報を含む入荷情報を取得する。
     *
     * @param array $details
     * @return void
     */
    public static function fetch(array $details) {

        // 動的に条件を設定
        $startDate = MtgJsonUtil::getIfExists(Con::START_DATE, $details);
        $endDate = MtgJsonUtil::getIfExists(Con::END_DATE, $details);
        $keyword = MtgJsonUtil::getIfExists(Con::CARD_NAME, $details);
        
        $results = self::select([
                'a.id',
                'a.attr',
                'a.cardname',
                'a.lang',
                'a.arrival_date',
                'a.vendor_type_id',
                'a.vcat',
                'a.vendor',
                'a.rank_number',
                'b.item_count',
                'b.sum_cost']
        )->fromSub(self::getLogQuery($keyword), 'a')->
        joinSub(self::getCountQuery(), 'b', function($join) {
            $join->on('a.arrival_date', '=', 'b.arrival_date')
                         ->on('a.vendor_type_id', '=', 'b.vendor_type_id');
        })->when($startDate, function($query, $startDate) {// 入荷日(開始)
            $query->where('a.arrival_date', '>=', $startDate);
        })->when($endDate, function($query, $endDate) {// 入荷日(終了)
            $query->where('a.arrival_date', '<=', $endDate);
        })->where('a.rank_number', 1)
        ->orderBy('a.arrival_date', 'desc')->get();
        return $results;
    }

    private static function getLogQuery(string $keyword) {
        $query = self::from('arrival_log as alog')
        ->select(
            'alog.id',
            'e.attr as attr',
            'c.name as cardname',
            's.language as lang',
            'alog.arrival_date',
            'alog.vendor_type_id',
            'v.name as vcat',
            'alog.vendor',
            DB::raw('row_number() over (PARTITION BY alog.arrival_date ORDER BY alog.id) as rank_number')
        )
        ->join('stockpile as s', 's.id', '=', 'alog.stock_id')
        ->join('card_info as c', 'c.id', '=', 's.card_id')
        ->join('expansion as e', 'e.notion_id', '=', 'c.exp_id')
        ->join('vendor_type as v', 'v.id', '=', 'alog.vendor_type_id')
        ->when($keyword, function ($query, $keyword) {
            // 検索条件が指定された場合
            $query->where('c.name', 'like', "%$keyword%");
        });
        return $query;
    }

    /**
     * 入荷日と取引先カテゴリ別の件数を取得する。
     *
     * @return query
     */
    private static function getCountQuery() {
        $query = DB::table('arrival_log')
                ->select(
                    DB::raw('count(id) as item_count'),
                    DB::raw('sum(cost) as sum_cost'),
                    'arrival_date',
                    'vendor_type_id'
                )
                ->groupBy('arrival_date', 'vendor_type_id')
                ->orderByDesc('arrival_date');

        return $query;
    }
}
