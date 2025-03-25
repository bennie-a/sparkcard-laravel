<?php

namespace Tests\Unit\DB\Card;

use App\Facades\CardInfoServ;
use App\Models\CardInfo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Services\Constant\CardConstant as Con;
use Tests\Trait\GetApiAssertions;

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertSame;

/**
 * カード情報検索に関するテスト
 */
class CardInfoSearchTest extends TestCase
{
    use GetApiAssertions;
    public function setup():void
    {
        parent::setup();
        parent::setUp();
        $this->seed('TruncateAllTables');
        $this->seed('DatabaseSeeder');
        $this->seed('TestExpansionSeeder');
        $this->seed('TestCardInfoSeeder');
        $this->seed('TestStockpileSeeder');
    }
    /**
     * 在庫情報の有無に関するテスト
     * @dataProvider stockpileprovider
     * @return void
     */
    public function test_stockpile(array $condition, int $quantity)
    {
        $query = [Con::NAME => $condition[0], Con::SET =>$condition [1],
                                 Con::COLOR => $condition[2], Con::IS_FOIL => $condition[3]];
        $response = $this->assert_OK($query);
        $json = $response->json();
        $this->assertCount(1, $json, '件数');
        foreach($json as $line) {
            $this->assertEquals($quantity, $line[Con::QUANTITY], '数量');
        }
    }

    public function stockpileprovider() {
        return [
            '在庫情報なし' => [['在庫情報なし', '', '', false], 0],
            '在庫が0' => [['ドラゴンの運命', '', '', false], 0],
            '在庫が1以上' => [['ドロスの魔神', '', '', false], 1],
            'カード番号に\'s\'が含まれている' => [['', 'XLN', 'W', true], 0],
            '色のみ' => [],
            'Foilのみ' => [],
            '全入力' => []
        ];
    }

    public function test_card() {

    }

    public function cardProvider() {
        
    }

    /**
     * エンドポイントを取得する。
     *
     * @return string
     */
    protected function getEndPoint():string {
        return 'api/database/card';
    }
}
