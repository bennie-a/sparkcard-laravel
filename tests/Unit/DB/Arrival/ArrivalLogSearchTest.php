<?php

namespace Tests\Unit\DB\Arrival;

use App\Models\ArrivalLog;
use App\Models\Stockpile;
use Tests\TestCase;
use App\Services\Constant\ArrivalConstant as ACon;
use App\Services\Constant\CardConstant as Con;
use App\Services\Constant\StockpileHeader as Header;
use App\Services\Constant\SearchConstant as SCon;
use App\Services\Constant\GlobalConstant as GCon;
use Illuminate\Http\Response;
use Tests\Database\Seeders\Arrival\TestArrivalLogSeeder;
use Tests\Database\Seeders\DatabaseSeeder;
use Tests\Database\Seeders\TestCardInfoSeeder;
use Tests\Database\Seeders\TestStockpileSeeder;
use Tests\Database\Seeders\TruncateAllTables;
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
        $this->seed(TruncateAllTables::class);
        $this->seed(DatabaseSeeder::class);
        $this->seed(TestCardInfoSeeder::class);
        $this->seed(TestStockpileSeeder::class);
        $this->seed(TestArrivalLogSeeder::class);
    }

    /**
     * 検索条件について検証する。
     *
     * @dataProvider conditionProvider
     * @param array $condition
     * @return void
     */
    public function test_condition(array $condition, $method) {        
        $response = $this->assert_OK($condition);
        $json = $response->json();
        
        $this->assertArrayHasKey(GCon::LOGS, $json, 'logs要素が含まれない');
        $method($condition, $json[GCon::DATA]);
    }
    
    public function conditionProvider() {
        $equal_date = fn($condition, $j) => 
                        $this->assertEquals($condition[ACon::ARRIVAL_DATE], $j[ACon::ARRIVAL_DATE]);
        $no_date = fn($condition, $j) => 
                        $this->assertArrayNotHasKey(ACon::ARRIVAL_DATE, $j);
        return [
            '検索条件が入荷日と取引先カテゴリ' =>
            [[ACon::ARRIVAL_DATE => TestDateUtil::formatToday(), SCon::VENDOR_TYPE_ID => 1], $equal_date],
            '検索条件がカード名と取引先カテゴリ' =>
            [[SCon::CARD_NAME => 'ドラゴン', SCon::VENDOR_TYPE_ID => 3], $no_date],
        ];
    }
    
    /**
     * 取引先カテゴリについて検証する。
     * @dataProvider vendorProvider
     * @param integer $vendor_type_id
     * @param callable $method
     * @return void
     */
    public function test_vendor(int $vendor_type_id, callable $method) {
        $condition = [SCon::VENDOR_TYPE_ID => $vendor_type_id,
                         ACon::ARRIVAL_DATE => TestDateUtil::formatToday()];
        $response = $this->assert_OK($condition);
        $json = $response->json();
        $this->assertArrayHasKey(GCon::DATA, $json, 'data要素が含まれない');

        $data = $json[GCon::DATA];
        $this->assertArrayHasKey(ACon::VENDOR, $data, 'vendor要素が含まれない');

        $logs = $json[GCon::LOGS];
        $method($data[ACon::VENDOR]);
    }
    
    /**
     * 取引先に関するテストケース
     * @dataProvider vendorProvider
     */
    public function vendorProvider() {
        return [
                '入荷先カテゴリがオリジナルパック' => [1, $this->verifyOtherVendor()],
                '入荷先カテゴリが私物' => [2, $this->verifyOtherVendor()],
                '入荷先カテゴリが買取' => [3, $this->verifyBuyVendor()],
                '入荷先カテゴリが棚卸し' => [4, $this->verifyOtherVendor()],
                '入荷先カテゴリが返品' => [5, $this->verifyOtherVendor()],
        ];
    }

    /**
     * カードのFoil要素について検証する。
     * @dataProvider isFoilProvider
     * @return void
     */
    public function test_isFoil(string $name, callable $method) {
        $condition = [SCon::CARD_NAME => $name, SCon::VENDOR_TYPE_ID => 1];
        $this->assertResult($condition, $method);
    }

    private function isFoilProvider() {
        return [
            'カード情報が通常版' => ['ドロスの魔神', $this->verifyNonFoil()],
            'カード情報がFoil版' => ['告別≪ショーケース≫', $this->verifyFoil()],
            'カード情報が特殊Foil版' => ['機械の母、エリシュ・ノーン', $this->verifyFoil()]
        ];
    }
    
    protected function assertResult(array $condition, callable $method) {
        $response = $this->assert_OK($condition);
        $json = $response->json();
        
        $this->assertArrayHasKey(GCon::LOGS, $json, 'logs要素が含まれない');

        $this->assertNotEmpty($json[GCon::LOGS], 'log要素の有無');
        foreach($json[GCon::LOGS] as $j) {
            $id = $j[GCon::ID];
            logger()->debug('入荷ID:'.$id);
            $this->assertNotEmpty($id, '入荷ID');

            $log = ArrivalLog::find($j[GCon::ID]);
            $this->assertEquals($log->cost, $j[Header::COST], '原価');
            logger()->debug("原価：expected:{$log->cost}, actual:{$j[Header::COST]}");

            $this->assertEquals($log->quantity, $j[Header::QUANTITY], '枚数');
            $this->verifyCard($log->stock_id, $j[Con::CARD]);
            $method($condition, $j, $log);
        }
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
        $exp_stock = Stockpile::where(GCon::ID, $stock_id)->first();
        $this->assertEquals($exp_stock->language, $json[Header::LANG], '言語');
        $this->assertEquals($exp_stock->condition, $json[Header::CONDITION], '状態');
    }
}
