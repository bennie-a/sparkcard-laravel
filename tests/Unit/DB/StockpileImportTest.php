<?php

namespace Tests\Unit\DB;

use App\Models\CardInfo;
use App\Models\Expansion;
use App\Models\Stockpile;
use App\Services\Constant\StockpileHeader;
use Illuminate\Foundation\Testing\DatabaseTransactions;
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
class StockpileImportTest extends TestCase
{
    use DatabaseTransactions;

    public function setup():void
    {
        parent::setup();
        $this->seed('DatabaseSeeder');
        $this->seed('TestExpansionSeeder');
        $this->seed('TestCardInfoSeeder');
        $this->seed('TestStockpileSeeder');
    }

    /**
     * A basic feature test example.
     *
     * @return void
     * @dataProvider importprovider
     */
    public function test_import(string $file, int $success, int $ignore, int $error, array $details)
    {
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
