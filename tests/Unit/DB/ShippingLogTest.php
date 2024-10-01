<?php

namespace Tests\Feature\tests\Unit\DB;

use App\Http\Response\CustomResponse;
use Illuminate\Http\Response;
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
    {        $json = ['total_rows' => 2, 'successful_rows' => 2,
                                'failed_rows' => 0, 'failed_details' => [],
                                'skip_rows' => 0, "skip_details" => []];
        $this->execute($filename, Response::HTTP_CREATED, $json);
    }
    
    public function okprovider() {
        return [
            '全件成功' => ['all_success.csv'],
            'ファイルの文字コードがShift-JIS' => ['shift-jis.csv'],
            'ファイルの文字コードがUTF-8' => ['utf-8.csv']
        ];
    }

    /**
     * 一部成功パターン
     *
     * @return void
     */
    public function test_partical() {
        $json = ['total_rows' => 2, 'successful_rows' => 1,
                         'failed_rows' => 1, 'failed_details' => [["number" => "3", "reason" => "該当するNotionカードがありません"]],
                          'skip_rows' => 0, "skip_details" => []];
        $this->execute("partical_success.csv", Response::HTTP_MULTI_STATUS, $json);
    }

    /**
     * OKパターン、一部OKパターンのテストを実行する。
     *
     * @param string $filename
     * @param integer $status
     * @param array $json
     * @return void
     */
    private function execute(string $filename, int $status, array $json) {
        $dir = dirname(__FILE__, 4).'\storage\test\sms\shipt\\';
        $response = $this->post('/api/shipping/import', ['path' => $dir.$filename]);
        logger()->debug($response->json());
        
        $response->assertStatus($status);
        $response->assertJson($json);
    }

    /**
     * NGケース
     *
     * @param string $filename
     * @param integer $status
     * @param array $json
     * @dataProvider ngprovider
     */
    public function test_ng(string $filename, int $status, array $json) {
        $this->execute($filename, $status, $json);
    }

    public function ngprovider() {
        return [
            'CSVヘッダー不足' => ['ng-noheader.csv', CustomResponse::HTTP_CSV_VALIDATION,
                     ["title" => "CSV Validation Error", "detail" => 'CSVファイルのヘッダーが足りません']],
            '重複した出荷登録' => ["duplicate.csv", 201,  
            ['total_rows' => 2, 'successful_rows' => 1,
            'failed_rows' => 0, 'failed_details' => [],
            'skip_rows' => 1, "skip_details" => [["number" => "3", "reason" => "既に登録されています"]]]],
            // '在庫情報が0件' => [],
            // '在庫情報が存在しない' => [],
            // '全件失敗' => ['partial_registration.csv'],
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
