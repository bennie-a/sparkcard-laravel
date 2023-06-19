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
            '全件成功_通常版' => ['stockpile_success.csv', 1, 0, 0, []],
            '全件成功_Foilカード' => ['stockpile_foil.csv', 1, 0, 0,  []],
            '一部スキップ_在庫情報が重複' => ['stockpile_duplicate.csv', 1, 1, 0, []],
            '一部エラー_カード情報がない' => ['stockpile_no_card_info.csv', 1, 0, 1, [3 => 'カードマスタ情報なし']],
            // '一部エラー_エキスパンションが登録されていない' => ['stockpile_not_found_setcode.csv', 0, 0, 1, []],
    ];
    }

    public function tearDown():void
    {
        CardInfo::query()->delete();
        Expansion::query()->delete();
    }
}
