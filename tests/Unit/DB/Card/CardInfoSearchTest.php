<?php

namespace Tests\Unit\DB\Card;

use Tests\TestCase;
use App\Services\Constant\CardConstant as Con;
use Tests\Database\Seeders\DatabaseSeeder;
use Tests\Database\Seeders\TestCardInfoSeeder;
use Tests\Database\Seeders\TestStockpileSeeder;
use Tests\Database\Seeders\TruncateAllTables;
use Tests\Trait\GetApiAssertions;

/**
 * カード情報検索に関するテスト
 */
class CardInfoSearchTest extends TestCase
{
    use GetApiAssertions;
    public function setup():void
    {
        parent::setup();
        $this->seed(TruncateAllTables::class);
        $this->seed(DatabaseSeeder::class);
        $this->seed(TestCardInfoSeeder::class);
        $this->seed(TestStockpileSeeder::class);
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
