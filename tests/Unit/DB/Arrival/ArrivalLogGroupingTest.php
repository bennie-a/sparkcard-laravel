<?php

namespace Tests\Unit\DB\Arrival;

use App\Libs\MtgJsonUtil;
use App\Models\ArrivalLog;
use App\Models\VendorType;

use Carbon\CarbonImmutable;
use Illuminate\Http\Response;
use Tests\TestCase;

use App\Services\Constant\StockpileHeader as Header;
use Illuminate\Support\Facades\DB;
use App\Services\Constant\SearchConstant as SCon;
use App\Services\Constant\ArrivalConstant as ACon;
use App\Services\Constant\CardConstant as Con;
use Tests\Trait\GetApiAssertions;
use Tests\Util\TestDateUtil;

/**
 * 入荷情報日付別検索のテストケース
 */
class ArrivalLogGroupingTest extends TestCase {

    use GetApiAssertions;

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

    /**
     * エンドポイントを取得する。
     *
     * @return string
     */
    protected function getEndPoint():string {
        return  'api/arrival/grouping';
    }
    /**
     * 入荷日検索に関するテストケース
     * @dataProvider arrivalDateProvider
     */
    public function test_ok_arrival_date(array $condition) {
        $response = $this->assert_OK($condition);
        $json = $response->json();

        if (MtgJsonUtil::isEmpty(SCon::START_DATE, $condition)) {
            $condition[SCon::START_DATE] = TestDateUtil::formatThreeDateBefore();
        }
        
        if (MtgJsonUtil::isEmpty(SCon::END_DATE, $condition)) {
            $condition[SCon::END_DATE] = TestDateUtil::formatToday();
        }
        
        // 並び順確認
        $first = current($json);
        $firstDay = CarbonImmutable::parse($condition[SCon::END_DATE]);
        $this->verifyJson($first, $firstDay);
        
        $last = end($json);
        $lastDay = CarbonImmutable::parse($condition[SCon::START_DATE]);
        $this->verifyJson($last, $lastDay);
    }

    public function arrivalDateProvider() {
        return [
            '入荷日_開始日のみ入力' => [[SCon::START_DATE => TestDateUtil::formatYesterday()]],
            '入荷日_終了日のみ入力' => [[SCon::END_DATE => TestDateUtil::formatTwoDateBefore()]],
            '入荷日_開始日と終了日の両方入力' => [[SCon::START_DATE => TestDateUtil::formatTwoDateBefore(),
            SCon::END_DATE => TestDateUtil::formatToday()]],
            '入荷日_開始日と終了日が同じ日' =>  [[SCon::START_DATE => TestDateUtil::formatToday(),
            SCon::END_DATE => TestDateUtil::formatToday()]],
            'カード名のみ入力' => [[SCon::CARD_NAME => 'ジン＝ギタクシアス']],
            '全検索項目入力' => [[SCon::CARD_NAME => '神', SCon::START_DATE => TestDateUtil::formatYesterday(),
            SCon::END_DATE => TestDateUtil::formatYesterday()]]
        ];
    }

    /**
     * カード名のみ検索に関するテストケース
     *
     * @param string $cardname カード名
     * @dataProvider cardnameProvider
     * @return void
     */
    public function test_ok_cardname(string $cardname) {
        $condition = [SCon::CARD_NAME => $cardname];
        $response = $this->assert_OK($condition);
        $json = $response->json();
        foreach($json as $j) {
            $this->assertTrue(str_contains($j[Header::NAME], $condition[SCon::CARD_NAME]));
            
            $log = $this->getCardInfoFromArrivalId($j['id']);
            $actual_foil = $j[Header::FOIL];
            $this->assertEquals($log['is_foil'], $actual_foil['is_foil']);
            if ($log->foiltag == '通常版') {
                $this->assertEmpty($actual_foil[Header::NAME]);
            } else {
                $this->assertEquals($log->foiltag, $actual_foil[Header::NAME], 'Foil名');
            }
        }
    }

    public function cardnameProvider() {
        return [
            '通常版のみ検索' => ['ドロスの魔神'],
            'Foil版のみ検索' => ['告別≪ショーケース≫'],
            '特殊Foil版のみ検索' => ['機械の母、エリシュ・ノーン≪ボーダレス「胆液」≫'],
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
                 =>[ [SCon::END_DATE => TestDateUtil::formatDate($four_days_before)]],
            '検索結果なし_カード名に一致する結果がない'
                =>[ [SCon::CARD_NAME => 'aaaa']]
        ];
    }

    /**
     * NGケース
     *
     * @dataProvider ngProvider
     * @param array $condition
     * @param [type] $msg
     * @return void
     */
    public function test_ng(array $condition, $msg) {
        $response = $this->execute($condition);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        $actual = $response->json();
        $this->assertEquals($msg, $actual['detail']);
    }

    public function ngProvider() {
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
    private function verifyJson(array $json, CarbonImmutable $day) {
        $day_string = TestDateUtil::formatDate($day);
        $log = $this->getCardInfoFromArrivalId($json[Con::ID]);

        $this->verifyCard($log->stock_id, $json[Con::CARD]);
        logger()->debug("入荷ログ検証：$day_string");
        $this->assertEquals($day_string, $json[ACon::ARRIVAL_DATE], "入荷日:$day_string");

        $vendor = $json[ACon::VENDOR];
        $this->assertNotNull($vendor, '取引先');
        $vendor_type_id = $vendor[SCon::VENDOR_TYPE_ID];

        $cost = $this->getCostSum($json[ACon::ARRIVAL_DATE], $vendor_type_id);
        $this->assertEquals($cost->item_count, $json['item_count'], "入荷件数:$day_string");

        $type = VendorType::find($vendor_type_id);
        $this->assertEquals($type->name, $vendor[Con::NAME], '取引先カテゴリ');

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
}