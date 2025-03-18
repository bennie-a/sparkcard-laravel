<?php

namespace App\Models;

use App\Libs\MtgJsonUtil;
use Illuminate\Database\Eloquent\Model;
use App\Services\Constant\StockpileHeader as Header;
use Illuminate\Support\Facades\DB;
use App\Services\Constant\SearchConstant as Con;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

use function PHPUnit\Framework\isEmpty;

class ArrivalLog extends Model
{
    protected $table = 'arrival_log';

    protected $fillable = ['id', 'stock_id',  Header::ARRIVAL_DATE, Header::QUANTITY,
                                            Header::COST, Header::VENDOR_TYPE_ID, Header::VENDOR];
    use HasFactory;

        /**
     * カード情報を含む入荷情報を取得する。
     *
     * @param array $details
     * @return Collection
     */
    public static function fetch(array $details) {
        // 動的に条件を設定
        $startDate = MtgJsonUtil::getValueOrEmpty(Con::START_DATE, $details);
        $endDate = MtgJsonUtil::getValueOrEmpty(Con::END_DATE, $details);
        $keyword = MtgJsonUtil::getValueOrEmpty(Con::CARD_NAME, $details);

        $subQuery = self::getTableQuery()
                        ->select([
                            'alog.id',
                            'e.attr as attr',
                            'c.name as cardname',
                            'c.isFoil as is_foil',
                            's.language as lang',
                            'alog.arrival_date',
                            'alog.vendor_type_id',
                            'v.name as vcat',
                            'alog.vendor',
                            'f.name as foiltag',
                            DB::raw("ROW_NUMBER() OVER (PARTITION BY alog.arrival_date, alog.vendor_type_id ORDER BY alog.id) as rank_number")
                        ]);
        $subQuery = self::join($subQuery);
        if(!empty($keyword)) {
            $pat = '%' . addcslashes($keyword, '%_\\') . '%';
            $subQuery->where('c.name', 'LIKE', $pat);
        }
        $alias = 'alog';
        $subQuery = self::addStartDateWhere($alias, $subQuery, $startDate);
        $subQuery = self::addEndDateWhere($alias, $subQuery, $endDate);

    $itemSummaryQuery = DB::table(self::make()->getTable())
                                                        ->select([
                                                            DB::raw('COUNT(id) as item_count'),
                                                            DB::raw('SUM(cost) as sum_cost'),
                                                            Header::ARRIVAL_DATE,
                                                            Header::VENDOR_TYPE_ID
                                                        ]);
    $itemSummaryQuery = self::addStartDateWhere('', $itemSummaryQuery, $startDate);
    $itemSummaryQuery = self::addEndDateWhere('', $itemSummaryQuery, $endDate);
    $itemSummaryQuery->groupBy(Header::ARRIVAL_DATE, Header::VENDOR_TYPE_ID);

    $mainQuery = DB::query()
                                    ->fromSub($subQuery, 'ranked_data')
                                    ->joinSub($itemSummaryQuery, 'item_summary',  function($join) {
                                        $join->on('ranked_data.arrival_date', '=', 'item_summary.arrival_date')
                                        ->on('ranked_data.vendor_type_id', '=', 'item_summary.vendor_type_id');
                                    })->where('ranked_data.rank_number', 1)
                            ->orderBy('ranked_data.arrival_date', 'desc');
    $result = $mainQuery->get();
    return $result;
}

/**
 * 複数条件に該当する入荷情報を取得する。
 *
 * @param array $details
 * @return Collection
 */
public static function filtering(array $details) {
    $log_conditions = array_filter($details, function($key) {
        return $key !== Con::CARD_NAME;
    }, ARRAY_FILTER_USE_KEY);
    $cardname = MtgJsonUtil::getValueOrEmpty(Con::CARD_NAME, $details);
    $columns = ['alog.id', 'alog.quantity', 'alog.cost', 'e.name as exp_name', 'e.attr as exp_attr', 'c.id', 'c.number',
    'c.name','c.image_url', 'c.isFoil', 's.condition', 'f.name as foiltag'];
    $query = self::getTableQuery()->select($columns);
    $query = self::join($query)->where($log_conditions)->
                    when($cardname, function($query, $cardname) {
                        return $query->where('c.name', $cardname);
                    });
    return $query->get();
}

/**
 * 内部結合のクエリを取得する。
 *
 * @param [type] $query
 * @return Builder
 */
private static function join($query):Builder {
    $query->join('stockpile as s', 's.id', '=', 'alog.stock_id')
    ->join('card_info as c', 'c.id', '=', 's.card_id')
    ->join('expansion as e', 'e.notion_id', '=', 'c.exp_id')
    ->join('vendor_type as v', 'v.id', '=', 'alog.vendor_type_id')
    ->join('foiltype as f', 'f.id', '=', 'c.foiltype_id');
    return $query;
}

private static function getTableQuery() {
    return DB::table('arrival_log as alog');
}
/**
 * 入荷日(開始日)の条件を追加する。
 *
 * @param $query
 * @param string|'' $arrivalDate
 * @return
 */
private static function addStartDateWhere(string $alias, $query, ?string $arrivalDate) {
    return self::addArrivalDateWhere($alias, $query, '>=', $arrivalDate);
}

/**
 * 入荷日(開始日)の条件を追加する。
 *
 * @param $query
 * @param string|'' $arrivalDate
 * @return
 */
private static function addEndDateWhere(string $alias, $query, ?string $arrivalDate) {
    return self::addArrivalDateWhere($alias, $query, '<=', $arrivalDate);
}

private static function addArrivalDateWhere(string $alias, $query, string $operator, ?string $arrivalDate) {
    $column = empty($alias)  ? Header::ARRIVAL_DATE : $alias.".".Header::ARRIVAL_DATE;
    return $query->when(!empty($arrivalDate), function ($q) use ($column, $operator, $arrivalDate) {
        $q->where($column, $operator, $arrivalDate);
    });
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
