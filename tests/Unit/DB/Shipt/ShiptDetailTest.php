<?php

namespace Tests\Unit\DB\Shipt;
use App\Http\Controllers\ShiptLogController;
use App\Models\Shipt\Orders;
use App\Services\Constant\GlobalConstant;
use Illuminate\Http\Response;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\Database\Seeders\DatabaseSeeder;
use Tests\Database\Seeders\Shipt\TestOrderSeeder;
use Tests\Database\Seeders\TestCardInfoSeeder;
use Tests\Database\Seeders\TestStockpileSeeder;
use Tests\Database\Seeders\TruncateAllTables;
use Tests\TestCase;
use App\Services\Constant\ShiptConstant as SC;
use Carbon\CarbonImmutable;
use Tests\Util\TestDateUtil;

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

    #[TestDox('顧客情報と金額情報を検証する。')]
    public function test_顧客情報と金額情報(): void
    {
        $order = Orders::inRandomOrder()->skip(1)->first();
        $this->assertNotNull($order, '期待値側の注文情報がありません');

        $response = $this->get('/api/shipping/'.$order->id);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(function(AssertableJson $json) use ($order) {
            $json->whereAll([
                GlobalConstant::ID => $order->id,
                SC::PLATFORM => $order->platform,
                SC::PLATFORM_ORDER_ID => $order->platform_order_id,
                SC::ZIPCODE => $order->zip_code,
                SC::ADDRESS => $order->address,
                SC::BUYER => $order->buyer_name,
                SC::SHIPPING_DATE => $order->shipt_date,
                SC::ITEM_COUNT => $order->item_count,
                SC::ITEM_SUBTOTAL => $order->item_subtotal,
                SC::GRAND_TOTAL => $order->grand_total,
                SC::PREV_ID => $order->previous()?->id,
                SC::NEXT_ID => $order->next()?->id
            ])->etc();

            // 発送日の形式チェック
            $json->whereType(SC::SHIPPING_DATE, 'string')
                ->where(SC::SHIPPING_DATE, function ($value) {
                    // yyyy/MM/dd にマッチする正規表現
                    return preg_match('/^\d{4}\/\d{2}\/\d{2}$/', $value) === 1;
                })
                ->etc();
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
