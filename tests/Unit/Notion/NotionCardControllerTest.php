<?php

namespace Tests\Feature;

use App\Services\Constant\SearchConstant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;
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
     * 最低価格に関するテストケース
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
            '最低価格が0' => [0],
            '最低価格が1以上' => [50]
        ];
    }

    /**
     * セット名による検索
     *
     * @param string $setname
     * @return void
     * @dataProvider expProvider
     */
    public function test_expansion(string $setname) {
        $actual = $this->execute(0, $setname);
        if (!empty($setname)) {
            foreach($actual as $a) {
                assertEquals($setname, $a->exp->name, 'エキスパンション名');
            }
        }
    }

    private function expProvider() {
        return [
            'セット名なし' => [''],
            'セット名あり' => ['神河：輝ける世界']
        ];
    }

    /**
     * テスト実行メソッド
     */
    private function execute(int $limitPrice, string $setname = '')
    {
        $status = 'ショップ登録予定';
        $query = ['status' => $status, 'price' => $limitPrice];
        if (!empty($setname)) {
            $query[SearchConstant::SET_NAME] = $setname;
        }
        $response = $this->json('GET','api/notion/card', $query);
        
        $response->assertStatus(200);
        $data = $response->baseResponse->content();
        $actual = json_decode($data);
        assertNotSame(0, count($actual), '検索件数');
        return $actual;
    }
}
