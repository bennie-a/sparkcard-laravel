<?php

namespace Tests\Unit\DB\Arrival;

use App\Libs\CarbonFormatUtil;
use App\Libs\MtgJsonUtil;
use App\Models\ArrivalLog;
use App\Models\Stockpile;
use Tests\TestCase;
use App\Services\Constant\ArrivalConstant as ACon;
use App\Services\Constant\CardConstant as Con;
use App\Services\Constant\StockpileHeader as Header;
use App\Services\Constant\SearchConstant as SCon;
use Illuminate\Http\Response;
use Tests\Trait\GetApiAssertions;
use Tests\Util\TestDateUtil;

/**
 * エンドポイントが'/api/arrival'のテストクラス
 */
class ArrivalLogSearchTest extends TestCase
{
    use GetApiAssertions {
        GetApiAssertions::verifyCard as verifyCardFromParent;
    }

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
     * 検索条件について検証する。
     *
     * @dataProvider conditionProvider
     * @param array $condition
     * @return void
     */
    public function test_condition(array $condition) {
        $method = function($condition, $j, $log){
            $id = $j[Con::ID];
            $this->assertNotEmpty($id, '入荷ID');
            logger()->debug('入荷ID:'.$id);
            if (MtgJsonUtil::isNotEmpty(ACon::ARRIVAL_DATE, $condition)) {
                $this->assertEquals(CarbonFormatUtil::toDateString($condition[ACon::ARRIVAL_DATE]), $j[ACon::ARRIVAL_DATE], '入荷日');
            } else {
                $exp_arrival = $log->arrival_date;
                $this->assertEquals(CarbonFormatUtil::toDateString($exp_arrival), $j[ACon::ARRIVAL_DATE], '入荷日');
            }
            $this->assertEquals($log->cost, $j[Header::COST], '原価');
            logger()->debug("原価：expected:{$log->cost}, actual:{$j[Header::COST]}");

            $this->assertEquals($log->quantity, $j[Header::QUANTITY], '枚数');
        };
        
        $this->assertResult($condition, $method);
    }
    
    public function conditionProvider() {
        return [
            '検索条件が入荷日と取引先カテゴリ' =>
            [[ACon::ARRIVAL_DATE => TestDateUtil::formatToday(), SCon::VENDOR_TYPE_ID => 1]],
            '検索条件がカード名と取引先カテゴリ' =>
            [[SCon::CARD_NAME => 'ドラゴン', SCon::VENDOR_TYPE_ID => 3]],
            '検索結果が通常版' => [[SCon::CARD_NAME => 'ドロスの魔神', SCon::VENDOR_TYPE_ID => 1]],
            '検索結果がFoil版' => [[SCon::CARD_NAME => '告別≪ショーケース≫', SCon::VENDOR_TYPE_ID => 2]],
            '検索結果が特殊Foil版' => 
                    [[SCon::CARD_NAME => '機械の母、エリシュ・ノーン≪ボーダレス「胆液」≫', SCon::VENDOR_TYPE_ID => 1]]
        ];
    }
    
    /**
     * 取引先に関するテストケース
     * @dataProvider vendorProvider
     */
    public function test_vendor(int $vendor_type_id) {
        $condition = [SCon::VENDOR_TYPE_ID => $vendor_type_id];
        $method = fn($condition, $j, $log) => 
                $this->verifyVendor($condition[SCon::VENDOR_TYPE_ID], $j[ACon::VENDOR]);
        $this->assertResult($condition, $method);
    }
    
    private function assertResult(array $condition, callable $method) {
        $response = $this->assert_OK($condition);
        $json = $response->json();
        
        foreach($json as $j) {
            $log = ArrivalLog::find($j[Con::ID]);
            $this->verifyCard($log->stock_id, $j[Con::CARD]);
            $method($condition, $j, $log);
        }
    }

    public function vendorProvider() {
        return [
            '入荷先カテゴリがオリジナルパック' => [1],
            '入荷先カテゴリが私物' => [2],
            '入荷先カテゴリが買取' => [3],
            '入荷先カテゴリが棚卸し' => [4],
            '入荷先カテゴリが返品' => [5],
        ];
    }

    /**
     * 検索結果が無い場合のテストケース
     *
     * @return void
     */
    public function test_NoResult() {
        $condition = [ACon::ARRIVAL_DATE => TestDateUtil::formatFourDateBefore(), SCon::VENDOR_TYPE_ID => 1];
        $this->assert_NG($condition, Response::HTTP_NOT_FOUND, '検索結果がありません。');
    }

    /**
     * その他NGケース
     * @dataProvider ngProvider
     * @return void
     */
    public function test_NG(array $condition, string $message) {
        $this->assert_NG($condition, Response::HTTP_BAD_REQUEST, $message);
    }
    
    public function ngProvider() {
        return [
            '取引先カテゴリIDが未入力' => [[], '取引先カテゴリIDは必ず入力してください。'],
            '入荷日が日付形式ではない' => 
                    [[ACon::ARRIVAL_DATE => 'aaa', SCon::VENDOR_TYPE_ID => 1], '入荷日が日付形式ではありません。']
        ];
    }
    
    /**
     * エンドポイントを取得する。
     *
     * @return string
     */
    protected function getEndPoint():string {
        return  'api/arrival';
    }

    protected function verifyCard($stock_id, array $json) {
        $this->verifyCardFromParent($stock_id, $json);
        $exp_stock = Stockpile::where(Con::ID, $stock_id)->first();
        $this->assertEquals($exp_stock->language, $json[Header::LANG], '言語');
        $this->assertEquals($exp_stock->condition, $json[Header::CONDITION], '状態');
    }
}
