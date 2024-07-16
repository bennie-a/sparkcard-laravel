<?php

namespace Tests\Feature\tests\Unit\DB;

use App\Facades\CardBoard;
use App\Models\CardInfo;
use App\Models\Expansion;
use App\Models\ShippingLog;
use App\Models\Stockpile;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Unit\Concerns\RefreshDatabaseLite;

/**
 * 出荷ログに関するテスト
 */
class ShippingLogTest extends TestCase
{
    use DatabaseTransactions;
    public function setup():void {
        parent::setup();
        // $this->seed('TestingDatabaseSeeder');
        $this->seed('DatabaseSeeder');
        $this->seed('TestExpansionSeeder');
        $this->seed('TestCardInfoSeeder');
        $this->seed('TestStockpileSeeder');
     }
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_import()
    {
        $dir = dirname(__FILE__, 4).'\storage\test\sms\\';
        $response = $this->post('/api/shipping/import', ['path' => $dir.'shipping_log.csv']);
        logger()->debug($response->json());

        $response->assertStatus(201);
        $response->assertJson(['row' => 4, 'success' => 2, 'skip' => 0, 'error' => 2, 'details' => [2 => '在庫情報なし', 5 => '在庫が0枚です']]);
    }


    // 全部成功
    // 全部失敗
    // 一部失敗
    public function tearDown():void
    {
        ShippingLog::query()->delete();
        Stockpile::query()->delete();
        CardInfo::query()->delete();
        Expansion::query()->delete();
    }
}
