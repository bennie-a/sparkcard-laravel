<?php

namespace Tests\Unit\DB\Arrival;

use App\Libs\MtgJsonUtil;
use App\Models\ArrivalLog;
use App\Models\VendorType;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Http\Response;
use Tests\TestCase;

use App\Services\Constant\StockpileHeader as Header;
use Illuminate\Support\Facades\DB;
use App\Services\Constant\SearchConstant as Con;

use function PHPUnit\Framework\assertTrue;
use function PHPUnit\Framework\isEmpty;

/**
 * 入荷情報検索のテストケース
 */
class ArrivalLogFetchTest extends TestCase {

    public function setUp(): void
    {
        parent::setUp();
        $this->seed('TruncateAllTables');
        $this->seed('DatabaseSeeder');
        $this->seed('TestExpansionSeeder');
        $this->seed('TestCardInfoSeeder');
        $this->seed('TestStockpileSeeder');
        $this->seed('TestArrivalLogSeeder');
    }

    private $url =  'api/arrival';

    /**
     * OKパターン
     * @dataProvider okProvider
     */
    public function test_ok(array $condition) {
        $response = $this->assert_OK($condition);
        $json = $response->json();

        if (MtgJsonUtil::isEmpty(Con::START_DATE, $condition)) {
            $condition[Con::START_DATE] = $this->formatDate(self::three_days_before());
        }
        
        if (MtgJsonUtil::isEmpty(Con::END_DATE, $condition)) {
            $condition[Con::END_DATE] = $this->formatDate(self::today());
        }
        
        // 並び順確認
        $first = current($json);
        $firstDay = CarbonImmutable::parse($condition[Con::END_DATE]);
        $this->verifyJson($first, $firstDay);
        
        $last = end($json);
        $lastDay = CarbonImmutable::parse($condition[Con::START_DATE]);
        $this->verifyJson($last, $lastDay);
    }

    public function okProvider() {
        return [
            '全件検索' => [[]],
            '入荷日_開始日のみ入力' => [[Con::START_DATE => $this->formatYesterday()]],
            '入荷日_終了日のみ入力' => [[Con::END_DATE => $this->formatTwoDateBefore()]],
            '入荷日_開始日と終了日の両方入力' => [[Con::START_DATE => $this->formatTwoDateBefore(),
                                                                                    Con::END_DATE => $this->formatToday()]],
            '入荷日_開始日と終了日が同じ日' =>  [[Con::START_DATE => $this->formatToday(),
                                                                                    Con::END_DATE => $this->formatToday()]],
            '全検索項目入力' => [[Con::CARD_NAME => '神', Con::START_DATE => $this->formatYesterday(),
                                                        Con::END_DATE => $this->formatYesterday()]]
        ];
    }

    /**
     * 検索結果がない場合のテストケース
     * @dataProvider noResultProvider
     * @param array $condition
     * @return void
     */
    public function test_NoResult(array $condition) {
        $response = $this->execute($condition);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $data = $response->json();
        $this->assertEquals('検索結果がありません。', $data['detail']);
    }

    public function noResultProvider() {
        $four_days_before = CarbonImmutable::today()->subDays(4);
        return [
            '検索結果なし_入荷日に該当する結果がない'
                 =>[ [Con::END_DATE => $this->formatDate($four_days_before)]],
            '検索結果なし_カード名に一致する結果がない'
                =>[ [Con::CARD_NAME => 'aaaa']]
        ];
    }

    /**
     * NGケース
     *
     * @dataProvider ng
     * @param array $condition
     * @param [type] $msg
     * @return void
     */
    public function test_ng(array $condition, $msg) {
        $response = $this->execute($condition);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function ngProvider() {
        return [
            '入荷日(開始日)が入荷日(終了日)以降' =>
                            [[Con::START_DATE => '2025/03/10', 'end_date' => '2025/03/01'], 
                                '入荷日(開始日)は入荷日(終了日)以前の日付を入力してください。']
        ];
    }

    private function formatToday() {
        $today = self::today();
        return $this->formatDate($today);
    }

    /**
     * 今日の日付を取得する。
     *
     * @return CarbonImmutable
     */
    private static function today():CarbonImmutable {
        return CarbonImmutable::today();
    }

    private function formatYesterday() {
        $yesterday = self::yesterday();
        return $this->formatDate($yesterday);
    }

    /**
     * 昨日の日付を取得する。
     *
     * @return CarbonImmutable
     */
    private static function yesterday():CarbonImmutable {
        return CarbonImmutable::yesterday();
    }

    private function formatTwoDateBefore() {
        $two_days_before = self::two_days_before();
        return $this->formatDate($two_days_before);
    }

    /**
     * 2日前の日付を取得する。
     *
     * @return CarbonImmutable
     */
    private static function two_days_before():CarbonImmutable {
        return CarbonImmutable::today()->subDays(2);
    }

    /**
     * 3日前の日付を取得する。
     *
     * @return CarbonImmutable
     */
    private static function three_days_before():CarbonImmutable {
        return CarbonImmutable::today()->subDays(3);
    }

    public function test_カード名検索() {
        $condition = [Con::CARD_NAME => 'ドラゴン'];
        $response = $this->assert_OK($condition);
        $json = $response->json();
        foreach($json as $j) {
            $this->assertTrue(str_contains($j['cardname'], $condition[Con::CARD_NAME]));
        }
    }

    private function assert_OK(array $condition) {
        $response = $this->execute($condition);
        $response->assertOk();
        return $response;
    }

    private function execute(array $condition) {
        $response = $this->json('GET', $this->url, $condition);
        return $response;
    }

    /**
     * 検索結果のJSONデータを検索する。
     *
     * @param array $json
     * @param CarbonImmutable $day
     * @return void
     */
    private function verifyJson(array $json, CarbonImmutable $day) {
        $day_string = $this->formatDate($day);
        logger()->debug("入荷ログ検証：$day_string");
        $this->assertEquals($day_string, $json[Header::ARRIVAL_DATE], "入荷日:$day_string");
        $this->assertEquals(3, $json['item_count'], "入荷件数:$day_string");
        $vendor = $json[Header::VENDOR];
        $this->assertNotNull($vendor, '取引先');

        $vendor_type_id = $vendor[Header::VENDOR_TYPE_ID];
        $type = VendorType::find($vendor_type_id);

        $cost = $this->getCostSum($json[Header::ARRIVAL_DATE], $vendor_type_id);
        $this->assertEquals(current($cost)->sum_cost, $json['sum_cost'], "原価合計:$day_string");
        

        $cardname = $json['cardname'];
        $log = $this->getCardInfoFromArrivalId($json['id']);

        $this->assertEquals($log->attr, $json['attr'], 'セット略称');
        $this->assertEquals($log->cardname, $cardname, 'カード名');
        $this->assertEquals($log->lang, $json[Header::LANG], '言語');
    }

    /**
     * 入荷情報のIDからカード情報を取得する。
     *
     * @param integer $id
     * @return ArrivalLog
     */
    private function getCardInfoFromArrivalId(int $id) {
        $log = ArrivalLog::join('stockpile as s', 'arrival_log.stock_id', '=', 's.id')
                                        ->join('card_info as c', 's.card_id', '=', 'c.id')
                                        ->join('expansion as e', 'e.notion_id', '=', 'c.exp_id')
                                        ->where('arrival_log.id', $id)
                                        ->select('arrival_log.id', 'e.attr as attr', 'c.name as cardname', 's.language as lang')
                                        ->first();
        return $log;
    }

    /**
     * 入荷日と入荷カテゴリIDから原価合計を取得する。
     *
     * @param String $arrival_date 入荷日
     * @param integer $vendor_type_id 入荷カテゴリID
     * @return collection
     */
    private function getCostSum(String $arrival_date, int $vendor_type_id) {
        $sum = DB::table('arrival_log')->
            select([DB::raw('SUM(cost) as sum_cost')])->
            where('vendor_type_id', $vendor_type_id)->where('arrival_date', $arrival_date)
            ->groupBy('arrival_date', 'vendor_type_id')->get();
            return current($sum);
    }

    private function formatDate(CarbonImmutable $day):string {
        $format = 'Y/m/d';
        return $day->format($format);
    }
}