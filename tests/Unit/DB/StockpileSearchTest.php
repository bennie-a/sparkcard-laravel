<?php

namespace Tests\Unit\DB;

use App\Models\CardInfo;
use App\Models\Expansion;
use App\Models\Stockpile;
use App\Services\Constant\SearchConstant;
use App\Services\Constant\StockpileHeader;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\Database\Seeders\DatabaseSeeder;
use Tests\Database\Seeders\TestCardInfoSeeder;
use Tests\Database\Seeders\TestStockpileSeeder;
use Tests\Database\Seeders\TruncateAllTables;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertJson;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertTrue;

/**
 * 在庫管理情報検索のテスト
 */
class StockpileSearchTest extends TestCase
{
    // use DatabaseTransactions;
    /**
     * テスト開始時のみテストデータを導入する。
     *
     * @return void
     */
    public function setup():void {
        parent::setup();
        $this->seed(TruncateAllTables::class);
        $this->seed(DatabaseSeeder::class);
        $this->seed(TestCardInfoSeeder::class);
        $this->seed(TestStockpileSeeder::class);
    }
    /**
     * 在庫情報検索OKパターン
     *
     * @param string $cardname
     * @param string $setname
     * @param integer $limit
     * @dataProvider searchprovider
     * @return void
     */
    public function test_index_ok(string $cardname, string $setname, int $limit, array $exStockNo) {
        $query = ['card_name' => $cardname, 'set_name' => $setname, 'limit' => $limit];
        $response = $this->call('GET', '/api/stockpile', $query);
        $response->assertStatus(Response::HTTP_OK);

        $expected = Stockpile::findMany($exStockNo);
        $actual = $response->json();
        assertEquals(count($expected), count($actual), '件数');
        foreach($expected as $index =>  $stock) {
            
            $info = CardInfo::find($stock->card_id);
            assertEquals($info->name, $actual[$index]['cardname']);
            assertEquals($stock->quantity, $actual[$index]['quantity']);
            assertEquals($stock->condition, $actual[$index]['condition']);
        }
    }

    /**
     * 在庫情報検索NGパターン
     * 
     * @return void
     */
    public function test_ng_not_found() {
        $query = [SearchConstant::CARD_NAME => 'xxxx', SearchConstant::SET_NAME => '', 'limit' => 0];
        $response = $this->call('GET', '/api/stockpile', $query);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $response->assertJson([
            'title' => '結果なし',
            'status' => Response::HTTP_NOT_FOUND,
            'detail' => '検索結果がありません。',
            "request" => "api/stockpile"
        ]);
    }


    public function searchprovider() {
        return [
            // '検索結果あり_検索条件なし' => ['', '', 0, [1,2,3,4,5]],
            '検索結果あり_カード名入力' => ['ファイレクシアの', '', 0, [1,2]],
            // '検索結果あり_セット名入力' => ['', '統一', 0, [4,5]],
            // '検索結果あり_取得件数あり' => ['', '', 2, [1,2]],
        ];
    }

    public function searchNgProvider() {
        return [
            '検索結果なし' =>['xxxx', '', 0, Response::HTTP_NO_CONTENT],
        ];
    }
}
