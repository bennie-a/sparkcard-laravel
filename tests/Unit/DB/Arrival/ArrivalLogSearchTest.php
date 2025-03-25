<?php

namespace Tests\Unit\DB\Arrival;

use App\Libs\MtgJsonUtil;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Services\Constant\StockpileHeader as Header;
use Tests\Trait\GetApiAssertions;
use Tests\Util\TestDateUtil;

use function PHPUnit\Framework\assertTrue;

class ArrivalLogSearchTest extends TestCase
{
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
     * 検索条件について検証する。
     *
     * @dataProvider conditionProvider
     * @param array $condition
     * @return void
     */
    public function test_condition(array $condition) {
        $method = function($condition, $j){
            $this->assertEquals($condition[Header::ARRIVAL_DATE], $j[Header::ARRIVAL_DATE], '入荷日');
            $this->verifyCard($j['card']);
        };

        $this->assertResult($condition, $method);
    }

    public function conditionProvider() {
        return [
            '検索条件が入荷日と取引先カテゴリ' =>
            [[Header::ARRIVAL_DATE => TestDateUtil::formatToday(), Header::VENDOR_TYPE_ID => 1]]
        ];       
    }

    /**
     * 取引先に関するテストケース
     * @dataProvider vendorProvider
     */
    public function test_vendor(int $vendor_type_id) {
        $condition = [Header::VENDOR_TYPE_ID => $vendor_type_id];
        $method = fn($condition, $j) => 
                $this->verifyVendor($condition[Header::VENDOR_TYPE_ID], $j[Header::VENDOR]);
        $this->assertResult($condition, $method);
    }

    private function assertResult(array $condition, callable $method) {
        $response = $this->assert_OK($condition);
        $json = $response->json();

        foreach($json as $j) {
            $method($condition, $j);
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

    // 検索条件が入荷日のみ、
    // 検索条件がカード名のみ、カード情報が通常版、Foil、特殊Foil
    
    /**
     * エンドポイントを取得する。
     *
     * @return string
     */
    protected function getEndPoint():string {
        return  'api/arrival/';
    }
}
