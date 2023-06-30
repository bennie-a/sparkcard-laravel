<?php

namespace Tests\Unit\DB;

use App\Models\CardInfo;
use App\Models\Expansion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertJson;
use function PHPUnit\Framework\assertNotNull;

/**
 * 在庫管理情報CSVインポート機能のテスト
 */
class StockpileImportTest extends TestCase
{
    public function setup():void
    {
        parent::setup();
        $this->neo =  Expansion::factory()->createOne(['name' => '神河：輝ける世界', 'attr' => 'NEO']);
        $draft = ['exp_id' => $this->neo->notion_id, 'name' => '発展の暴君、ジン＝ギタクシアス',
         'en_name' => 'Jin-Gitaxias, Progress Tyrant', 'color_id' => 'U', 'number' => '59',
          'isFoil' => false, 'image_url' => ''];
        CardInfo::factory()->createOne($draft);
        $foil = ['exp_id' => $this->neo->notion_id, 'name' => '発展の暴君、ジン＝ギタクシアス',
          'en_name' => 'Jin-Gitaxias, Progress Tyrant', 'color_id' => 'U', 'number' => '59',
           'isFoil' => true, 'image_url' => ''];
        CardInfo::factory()->createOne($foil);
        $specific = ['exp_id' => $this->neo->notion_id, 'name' => '告別≪ショーケース≫',
        'en_name' => 'Farewell', 'color_id' => 'W', 'number' => '365',
         'isFoil' => true, 'image_url' => ''];
         CardInfo::factory()->createOne($specific);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     * @dataProvider dataprovider
     */
    public function test_execute(string $file, int $success, int $ignore, int $error, array $details)
    {
        $dir = dirname(__FILE__, 4).'\storage\test\sms\\';
        $response = $this->post('api/stockpile/import', ['path' => $dir.$file]);

        $response->assertStatus(201);
        $exRow = $success + $ignore + $error;
        $response->assertJson(['row' => $exRow, 'skip' => $ignore, 'error' => $error, 'details' => $details]);
        if ($success > 0) {
            $stock = DB::table('stockpile')->first();
            assertNotNull($stock, '在庫情報の有無');
            assertEquals('JP', $stock->language, '在庫情報の言語');
        }
    }

    public function dataprovider() {
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
        CardInfo::query()->delete();
        Expansion::query()->delete();
    }
}
