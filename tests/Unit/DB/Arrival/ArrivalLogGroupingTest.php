<?php

namespace Tests\Unit\DB\Arrival;

use App\Models\ArrivalLog;
use App\Models\Stockpile;

use Carbon\CarbonImmutable;
use Illuminate\Http\Response;
use Tests\TestCase;

use App\Services\Constant\StockpileHeader as Header;
use Illuminate\Support\Facades\DB;
use App\Services\Constant\SearchConstant as SCon;
use App\Services\Constant\ArrivalConstant as ACon;
use App\Services\Constant\CardConstant as Con;
use App\Services\Constant\GlobalConstant as GCon;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Database\Seeders\DatabaseSeeder;
use Tests\Database\Seeders\Arrival\TestArrivalLogSeeder;
use Tests\Database\Seeders\TestCardInfoSeeder;
use Tests\Database\Seeders\TestStockpileSeeder;
use Tests\Database\Seeders\TruncateAllTables;
use Tests\Trait\GetApiAssertions;
use Tests\Util\TestDateUtil;

/**
 * 入荷情報日付別検索のテストケース
 */
class ArrivalLogGroupingTest extends TestCase {

    use GetApiAssertions {
        GetApiAssertions::verifyCard as verifyCardFromParent;
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(TruncateAllTables::class);
        $this->seed(DatabaseSeeder::class);
        $this->seed(TestCardInfoSeeder::class);
        $this->seed(TestStockpileSeeder::class);
        $this->seed(TestArrivalLogSeeder::class);
    }
    
    /**
     * エンドポイントを取得する。
    *
    * @return string
    */
    protected function getEndPoint():string {
        return  'api/arrival/grouping';
    }
    
    public function test_入荷日_開始日のみ入力() {
        
        $condition = [SCon::START_DATE => TestDateUtil::formatYesterday()];
        $this->verifyArrivalDate($condition, $this->equalBefore());
    }
    
    public function test_入荷日_終了日のみ入力() {
        $condition = [SCon::END_DATE => TestDateUtil::formatTwoDateBefore()];
        $this->verifyArrivalDate($condition, $this->equalAfter());
    }

    public function 入荷日_開始日と終了日の両方入力() {
        $condition = [SCon::START_DATE => TestDateUtil::formatTwoDateBefore(),
                      SCon::END_DATE => TestDateUtil::formatToday()];
        $this->verifyArrivalDate($condition, $this->between());
    }

    public function 入荷日_開始日と終了日が同じ日() {
       $condition = [SCon::START_DATE => TestDateUtil::formatToday(),
            SCon::END_DATE => TestDateUtil::formatToday()];
        $this->verifyArrivalDate($condition, $this->equals());
    }

    public function カード名のみ入力() {
        $condition = [SCon::CARD_NAME => 'ジン＝ギタクシアス'];
        $this->verifyArrivalDate($condition, $this->cardname());
    }

    public function 全検索項目入力() {
         $condition = [SCon::CARD_NAME => '神', SCon::START_DATE => TestDateUtil::formatYesterday(),
            SCon::END_DATE => TestDateUtil::formatYesterday()];
        $this->verifyArrivalDate($condition, $this->condition_all());
    }

    /**
     * 入荷日検索に関するテストケース
     */
    public function verifyArrivalDate(array $condition, callable $method) {
        $response = $this->assert_OK($condition);
        $json = $response->json();

        foreach($json as $j) {
            $this->verifyJson($j);
            $log = ArrivalLog::find($j[GCon::ID]);
            $method($condition, $j, $log);
        }
    }

    private function equals() {
        return function($condition, $json, $log) {
            $start = new Carbon($condition[SCon::START_DATE]);
            $end = new Carbon($condition[SCon::END_DATE]);
            $actual = new Carbon($json[ACon::ARRIVAL_DATE]);
            $this->assertTrue($start->eq($actual), '入荷日が開始日と異なる');
            $this->assertTrue($end->eq($actual), '入荷日が終了日と異なる');
        };
    }

    private function equalBefore() {
        return function($condition, $json, $log) {
            $expected = new Carbon($condition[SCon::START_DATE]);
            $actual = new Carbon($json[ACon::ARRIVAL_DATE]);
            $this->assertTrue($expected->lte($actual), '入荷日が開始日以前');
        };
    }

    private function equalAfter() {
        return function($condition, $json, $log) {
            $expected = new Carbon($condition[SCon::END_DATE]);
            $actual = new Carbon($json[ACon::ARRIVAL_DATE]);
            $this->assertTrue($expected->gte($actual), '入荷日が開始日以降');
        };
    }

    private function between() {
        return function($condition, $json, $log) {
            $start = new Carbon($condition[SCon::START_DATE]);
            $end = new Carbon($condition[SCon::END_DATE]);
            $actual = new Carbon($json[ACon::ARRIVAL_DATE]);
            $this->assertTrue($actual->between($start, $end), '入荷日が検索条件に合わない');
        };
    }

    private function cardname() {
        return function($condition, $json, $log) {
            $keyword = $condition[SCon::CARD_NAME];
            $card = $json[Con::CARD];
            $this->assertTrue(str_contains($card[Con::NAME], $keyword), 'カード名');
        };
    }

    private function condition_all() {
        return function($condition, $json, $log) {
            $keyword = $condition[SCon::CARD_NAME];
            $card = $json[Con::CARD];
            $this->assertTrue(str_contains($card[Con::NAME], $keyword), 'カード名');

            $start = new Carbon($condition[SCon::START_DATE]);
            $end = new Carbon($condition[SCon::END_DATE]);
            $actual = new Carbon($json[ACon::ARRIVAL_DATE]);
            $this->assertTrue($actual->between($start, $end), '入荷日が検索条件に合わない');
        };
    }
    
    public function test_通常版のみ検索() {
        $this->verifyIsFoil('ドロスの魔神', $this->verifyNonFoil());
    } 
    
    public function test_Foil版のみ検索() {
        $this->verifyIsFoil('告別', $this->verifyFoil(), 2);
    } 
    
    public function test_特殊Foil版のみ検索() {
        $this->verifyIsFoil('機械の母、エリシュ・ノーン', $this->verifyFoil(), 23);
    } 

    /**
     * カード名のみ検索に関するテストケース
     *
     * @param string $cardname カード名
     * @param callable $method 検証メソッド
     * @param int  $promo_id プロモタイプID(期待値)
     * @return void
     */
    public function verifyIsFoil(string $cardname, callable $method, int $promo_id = 1) {
        $condition = [SCon::CARD_NAME => $cardname];
        $json = $this->ok($condition, $method);
        $card = current($json)[Con::CARD];
        $this->verifyPromotype($promo_id, $card);
    }
    
    /**
     * vendor要素について検証する。
     *
     * @param integer $vendor_type_id
     * @param callable $method
     * @return void
     */
    public function verifyVendor(int $vendor_type_id, callable $method) {
        $condition = [SCon::CARD_NAME => "告別", SCon::START_DATE => TestDateUtil::formatToday()];
        $response = $this->assert_OK($condition);
        $json = $response->json();

        $filtered = array_filter($json, function($j) use ($vendor_type_id) {
                                return $j[ACon::VENDOR][GCon::ID] == $vendor_type_id;
                            });
        $this->assertNotEmpty($filtered, '検索結果');
        foreach($filtered as $f) {
            $method($f[ACon::VENDOR]);
        }
    }

    #[DataProvider('vendorProvider')]
    public function test_otherVendor(int $vendor_type_id) {
        $this->verifyVendor($vendor_type_id, $this->verifyOtherVendor());
    }

    /**
     * 取引先に関するテストケース
     */
    public static function vendorProvider() {
        return [
                '入荷先カテゴリがオリジナルパック' => [1],
                '入荷先カテゴリが私物' => [2],
                '入荷先カテゴリが棚卸し' => [4],
                '入荷先カテゴリが返品' => [5],
        ];
    }
    
    public function test_入荷先カテゴリが買取() {
        $this->verifyVendor(3, $this->verifyBuyVendor());
    }

    private function ok($condition, callable $method) {
        $response = $this->assert_OK($condition);
        $json = $response->json();
        foreach($json as $j) {
            $log = $this->getCardInfoFromArrivalId($j[GCon::ID]);
            $this->verifyCard($log->stock_id, $j[Con::CARD]);           
            $method($condition, $j, $log);
        }
        return $json;
    }
    /**
     * 検索結果がない場合のテストケース
     * @param array $condition
     * @return void
     */
    #[DataProvider('noResultProvider')]
    public function test_NoResult(array $condition) {
        $response = $this->execute($condition);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $data = $response->json();
        $this->assertEquals('検索結果がありません。', $data['detail']);
    }

    public static function noResultProvider() {
        $four_days_before = CarbonImmutable::today()->subDays(4);
        return [
            '検索結果なし_入荷日に該当する結果がない'
                 =>[ [SCon::END_DATE => TestDateUtil::formatDate($four_days_before)]],
            '検索結果なし_カード名に一致する結果がない'
                =>[ [SCon::CARD_NAME => 'aaaa']]
        ];
    }

    /**
     * NGケース
     *
     * @param array $condition
     * @param [type] $msg
     * @return void
     */
    #[DataProvider('ngProvider')]
    public function test_ng(array $condition, $msg) {
        $response = $this->execute($condition);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        $actual = $response->json();
        $this->assertEquals($msg, $actual['detail']);
    }

    public static function ngProvider() {
        return [
            '入荷日(開始日)が入荷日(終了日)以降' =>
                            [[SCon::START_DATE => '2025/03/10', 'end_date' => '2025/03/01'], 
                                '入荷日(開始日)は入荷日(終了日)以前の日付を入力してください。']
        ];
    }

    /**
     * 検索結果のJSONデータを検索する。
     *
     * @param array $json
     * @param CarbonImmutable $day
     * @return void
     */
    private function verifyJson(array $json) {
        $day_string =$json[ACon::ARRIVAL_DATE];
        $log = $this->getCardInfoFromArrivalId($json[GCon::ID]);

        $this->verifyCard($log->stock_id, $json[Con::CARD]);
        logger()->debug("入荷ログ検証：$day_string");

        $vendor = $json[ACon::VENDOR];
        $vendor_type_id = $vendor[GCon::ID];

        $cost = $this->getCostSum($json[ACon::ARRIVAL_DATE], $vendor_type_id);
        $this->assertEquals($cost->item_count, $json['item_count'], "入荷件数:$day_string");

        $this->assertEquals($cost->sum_cost, $json['sum_cost'], "原価合計:$day_string");
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
                                        ->join('foiltype as f', 'f.id', '=', 'c.foiltype_id')
                                        ->where('arrival_log.id', $id)
                                        ->select('arrival_log.id', 'arrival_log.stock_id', 'e.attr as attr', 'c.name as cardname',
                                                                                         's.language as lang', 'c.isFoil as is_foil', 'f.name as foiltag')
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
            select([DB::raw('SUM(cost) as sum_cost'), DB::raw('COUNT(id) as item_count')])->
            where('vendor_type_id', $vendor_type_id)->where('arrival_date', $arrival_date)
            ->groupBy('arrival_date', 'vendor_type_id')->first();
            return $sum;
    }

    protected function verifyCard($stock_id, array $json) {
        $this->verifyCardFromParent($stock_id, $json);
        $this->assertArrayHasKey(Header::LANG, $json, 'lang要素なし');
        $exp_stock = Stockpile::where(GCon::ID, $stock_id)->first();
        $this->assertEquals($exp_stock->language, $json[Header::LANG], '言語');
    }
}