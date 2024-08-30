<?php

namespace Tests\Feature\tests\Unit\DB;

use Tests\TestCase;

/**
 * 出荷ログに関するテスト
 */
class ShippingLogTest extends TestCase
{
    public function setup():void {
        parent::setup();
        $this->seed('TruncateAllTables');
        $this->seed('DatabaseSeeder');
        $this->seed('TestExpansionSeeder');
        $this->seed('TestCardInfoSeeder');
        $this->seed('TestStockpileSeeder');
     }
    /**
     * A basic feature test example.
     *@dataProvider okprovider
     * @return void
     */
    public function test_ok(String $filename)
    {
        $dir = dirname(__FILE__, 4).'\storage\test\sms\shipt\\';
        $response = $this->post('/api/shipping/import', ['path' => $dir.$filename]);
        logger()->debug($response->json());
        
        $response->assertStatus(201);
        // $response->assertJson(['row' => 4, 'success' => 2, 'skip' => 0, 'error' => 2, 'details' => [2 => '在庫情報なし', 5 => '在庫が0枚です']]);
    }
    
    public function okprovider() {
        return [
            '全件成功' => ['all_success.csv']
            // '一部登録済み' => ['partial_registration.csv']
        ];
    }

    /**
     * 文字コード別CSVファイル読み込みテスト
     * @dataProvider encodeProvider
     * @return void
     */
    public function test_encode() {

    }

    public function encodeProvider() {
        return [
            'ファイルの文字コードがShift-JIS' => [],
            'ファイルの文字コードがUTF-8' => []
        ];
    }

    public function ngprovider() {
        return [
            '一部失敗' => ['all_success.csv'],
            '全件失敗' => ['partial_registration.csv'],
        ];

    }

    // 全部成功
    // 出荷記録が登録済み
    // 全部失敗
    // 一部失敗
    public function tearDown():void
    {
        // ShippingLog::query()->delete();
        // Stockpile::query()->delete();
        // CardInfo::query()->delete();
        // Expansion::query()->delete();
    }

}
