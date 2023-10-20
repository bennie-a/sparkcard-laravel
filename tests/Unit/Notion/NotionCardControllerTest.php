<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertNotSame;
use function PHPUnit\Framework\assertTrue;

/**
 * Notion API連携に関するテストケース
 */
class NotionCardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp():void {
        parent::setUp();
        $this->seed('TestExpansionSeeder');
    }

    /**
     * 下限価格に関するテストケース
     *@dataProvider limitPriceProvider
     * @return void
     */
    public function test_limitPrice(int $limitPrice) {
        $actual = $this->execute($limitPrice);
        foreach($actual as $a) {
            assertTrue($a->price >= $limitPrice, '商品価格が最低価格以上');
        }
    }

    public function limitPriceProvider() {
        return [
            'limitPriceが0' => [0],
            'limitPriceが1以上' => [50]
        ];
    }

    /**
     * テスト実行メソッド
     */
    public function execute(int $limitPrice)
    {
        $status = 'ショップ登録予定';
        $query = ['status' => $status, 'limitPrice' => $limitPrice];
        $response = $this->json('GET','api/notion/card', $query);
        
        $response->assertStatus(200);
        $data = $response->baseResponse->content();
        $actual = json_decode($data);
        assertNotSame(0, count($actual), '検索件数');
        return $actual;
    }
}
