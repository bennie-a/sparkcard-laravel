<?php

namespace Tests\Unit\DB;

use App\Models\CardInfo;
use App\Models\Expansion;
use App\Models\Stockpile;
use App\Services\Constant\StockpileHeader;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertJson;
use function PHPUnit\Framework\assertNotNull;

/**
 * 在庫管理情報のテスト
 */
class StockpileTest extends TestCase
{
    public function setup():void
    {
        parent::setup();
        // $this->seed('DatabaseSeeder');
        // $this->seed('TestExpansionSeeder');
        // $this->seed('TestCardInfoSeeder');
        // $this->seed('TestStockpileSeeder');
    }

    /**
     * A basic feature test example.
     *
     * @return void
     * @dataProvider importprovider
     */
    public function test_import(string $file, int $success, int $ignore, int $error, array $details)
    {
        $this->markTestSkipped('一時中断');
        $dir = dirname(__FILE__, 4).'\storage\test\sms\\';
        $response = $this->post('api/stockpile/import', ['path' => $dir.$file]);

        $response->assertStatus(201);
        $exRow = $success + $ignore + $error;
        $response->assertJson(['row' => $exRow, 'success' => $success, 'skip' => $ignore, 'error' => $error, 'details' => $details]);
        if ($success > 0) {
            $stock = DB::table('stockpile')->first();
            assertNotNull($stock, '在庫情報の有無');
            assertEquals('JP', $stock->language, '在庫情報の言語');
        }
    }

    /**
     * 在庫情報検索のテスト
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
            assertEquals($stock->quantity, $actual[$index][StockpileHeader::QUANTITY]);
            assertEquals($stock->condition, $actual[$index][StockpileHeader::CONDITION]);
        }

    }

    public function searchprovider() {
        return [
            '検索結果あり_検索条件なし' => ['', '', 0, [1,2,3,4,5]],
            '検索結果あり_カード名入力' => ['ファイレクシアの', '', 0, [1,2]],
            // '検索結果あり_セット名入力' => [],
            // '検索結果あり_取得件数あり' => [],
            // '検索結果あり_取得件数なし' => [],
            // '検索結果なし' =>[],
        ];
    }

    public function importprovider() {
        return [
            '成功_カード情報あり_通常版' => ['stockpile_success.csv', 1, 0, 0, []],
            '成功_カード情報あり_Foilカード' => ['stockpile_foil.csv', 1, 0, 0,  []],
            '成功_カード情報あり_特別カード' => ['stockpile_specific.csv', 1, 0, 0,  []],
            '成功_CSVにセット名なし_英語カード名あり' => ['setcode.csv', 2, 0, 0, []],
            '成功_セット情報あり_カード情報なし_APIにあり' => ['stockpile_nocard_apiok.csv', 1, 0, 0,  []],
            '成功_セット情報とカード情報なし_APIに両方あり' => ['stockpile_noset_nocard_apiok.csv', 1, 0, 0, []],
            '登録スキップ_在庫情報が重複' => ['stockpile_duplicate.csv', 1, 1, 0, []],
            'エラー_セット情報あり_カード情報なしAPIになし' => ['stockpile_no_card_info.csv', 0, 0, 1, [2 => 'APIに該当カードなし']],
            'エラー_セット情報あり_カード情報なし_カードが特別版' => ['stockpile_error_nocard_specific.csv', 0, 0, 1, [2 => '特別版はマスタ登録できません']],
            'エラー_エキスパンションなし_APIになし' => ['stockpile_noset_aping.csv', 0, 0, 1, [2 => 'APIに該当セットなし']],
        ];
    }

    public function tearDown():void
    {
        // Artisan::call('migrate:refresh');
        parent::tearDown();
    }
}
