<?php

namespace Tests\Unit\DB\Shipt;
use App\Http\Controllers\ShiptLogController;
use App\Models\Shipt\Orders;
use Illuminate\Http\Response;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\Database\Seeders\DatabaseSeeder;
use Tests\Database\Seeders\Shipt\TestOrderSeeder;
use Tests\Database\Seeders\TestCardInfoSeeder;
use Tests\Database\Seeders\TestStockpileSeeder;
use Tests\Database\Seeders\TruncateAllTables;
use Tests\TestCase;
use App\Services\Constant\GlobalConstant as GC;

#[TestDox('注文情報詳細機能に関するテスト')]
#[CoversTestClass(ShiptLogController::class)]
class ShiptDetailTest extends TestCase
{
    public function setup():void {
        parent::setup();
        $this->seed(TruncateAllTables::class);
        $this->seed(DatabaseSeeder::class);
        $this->seed(TestCardInfoSeeder::class);
        $this->seed(TestStockpileSeeder::class);
        $this->seed(TestOrderSeeder::class);
    }

    /**
     * A basic feature test example.
     */
    public function test_通常版(): void
    {
        $order = Orders::inRandomOrder()->first();
        $this->assertNotNull($order);

        $response = $this->get('/api/shipping/'.$order->id);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(function(AssertableJson $json) use ($order) {
            $json->where(
                GC::ID,$order->id
            )->etc();
        });
    }

    // 通常版
    // Non-foil版
    // Foil版
    // Promo版
    // 送料_ミニレター
    // 送料_クリックポスト
    // 送料_簡易書留
    // 最初のレコードを表示⇒prev_idが0
    // 最後のレコードを表示⇒next_idが0

    // エラー_注文情報が存在しない
    // IDが数字以外
}
