<?php

namespace Tests\Unit\DB;

use App\Facades\CardInfoServ;
use App\Models\CardInfo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Services\Constant\CardConstant as Con;

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertSame;

/**
 * カード情報検索に関するテスト
 */
class CardInfoSearchTest extends TestCase
{
    use RefreshDatabase;
    public function setup():void
    {
        parent::setup();
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
        $contents = $this->execute($condition);
        assertCount(1, $contents, '件数');
        foreach($contents as $line) {
            assertSame($quantity, $line[Con::QUANTITY], '数量');
        }
    }

    public function execute(array $condition, int $statuscode = 200) {
        $query = [Con::NAME => $condition[0], Con::SET =>$condition [1],
                                 Con::COLOR => $condition[2], Con::IS_FOIL => $condition[3]];
        $response = $this->json('GET', 'api/database/card', $query);
        $response->assertStatus($statuscode);
        $json = $response->baseResponse->getContent();
        return  json_decode($json, true);
    }

    public function stockpileprovider() {
        return [
            '在庫情報なし' => [['エリシュ・ノーン', '', '', false], 0],
            '在庫が0' => [['ドラゴンの運命', '', '', false], 0],
            '在庫が1以上' => [['ドロスの魔神', '', '', false], 1]
            // 'セット名のみ' => [],
            // '色のみ' => [],
            // 'Foilのみ' => [],
            // '全入力' => []
        ];
    }
}
