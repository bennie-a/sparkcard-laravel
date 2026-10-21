<?php

namespace Tests\Unit\CLI;

use App\Enum\ShiptMethod;
use App\Enum\ShopPlatform;
use App\Enum\SortOrder;
use App\Models\CardInfo;
use App\Models\Expansion;
use App\Models\Promotype;
use App\Models\Shipping;
use App\Models\ShippingLog;
use App\Models\Shipt\OrderItem;
use App\Models\Shipt\Orders;
use App\Services\Constant\CardConstant;
use App\Services\Constant\GlobalConstant as GC;
use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\Database\Seeders\CLI\Divide\DivideShippingLogSeeder;
use Tests\Database\Seeders\DatabaseSeeder;
use Tests\Database\Seeders\TruncateAllTables;
use Tests\TestCase;
use App\Services\Constant\ShiptConstant as SC;
use PHPUnit\Framework\Attributes\TestWith;

#[TestDox('divide:orderコマンドのテスト')]
#[CoversClass(App\Console\Commands\DivideOrder::class)]
class DivideOrderTest extends TestCase
{

    public function setup(): void
    {
        parent::setUp();
        logger()->info('マスタデータ登録開始');
        $this->seed(TruncateAllTables::class);
        $this->seed(DatabaseSeeder::class);
        $this->seed(DivideShippingLogSeeder::class);
        logger()->info('マスタデータ登録終了');
    }

    #[Test]
    #[TestDox('購入者情報を検証する。')]
    public function buyerInfo(): void
    {
        $this->execute();
        $orders = Orders::all();
        $log = ShippingLog::orderBy('shipping_date', SortOrder::ASC->value)->get();
        $uniqueLog = $log->unique('order_id');
        $this->assertCount($uniqueLog->count(), $orders, 'ordersテーブルへの登録件数');
        foreach($uniqueLog as $u) {
            $this->assertDatabaseHas(Orders::class, [
                SC::PLATFORM_ORDER_ID => $u->order_id,
                SC::ZIPCODE => $u->zip_code,
                SC::ADDRESS => $u->address,
                SC::BUYER => $u->name,
                SC::COUPON_DISCOUNT => 0,
                SC::SHIPT_DATE => $u->shipping_date
            ]);
        }

        $items = OrderItem::all();
        $this->assertCount($log->count(), $items, 'order_itemテーブルの登録件数');
    }

    #[Test]
    #[TestDox('注文情報のプラットフォームを検証する。')]
    #[TestWith([ShopPlatform::MERCARI, 'order_'], 'メルカリShops')]
    #[TestWith([ShopPlatform::DIRECT, 'd2c_'], '直接取引')]
    #[TestWith([ShopPlatform::BASE, ''], 'BASEショップ')]
    public function platform(ShopPlatform $platform, string $prefix) {
        $this->execute();
        $query = null;
        if ($platform === ShopPlatform::BASE) {
            $query = ShippingLog::where('order_id', 'not like', 'order_%')->where('order_id', 'not like', 'd2c_%');
        } else {
            $query = ShippingLog::whereLike('order_id', "{$prefix}_%");
        }
        $log = $query->orderBy('shipping_date', SortOrder::ASC->value)->get();
        $uniqueLog = $log->unique('order_id');
        foreach($uniqueLog as $u) {
            $this->assertDatabaseHas(Orders::class, [
                SC::PLATFORM => $platform->value,
                SC::PLATFORM_ORDER_ID => $u->order_id,
            ]);
        }
    }

    #[Test]
    #[TestDox('注文情報の送料に関する判定について検証する。')]
    #[TestWith([ShopPlatform::DIRECT, ShiptMethod::DIRECT, false], '手渡し')]
    public function shiptFee(ShopPlatform $platform, ShiptMethod $method, bool $isDeleted) {
        $this->execute();
        $expected = Shipping::when($isDeleted, function($query) {
            return $query->inactive();
        }, function($query) {
            return $query->active();
        })->where(GC::NAME, $method->value)->sole();
        $this->assertDatabaseHas(Orders::class, [
            SC::PLATFORM => $platform->value,
            SC::FEE_ID => $expected->id,
        ]);
    }

    private function execute() {
        $exitCode  = Artisan::call('divide:order');
        logger()->info(Artisan::output());
        $this->assertEquals(0, $exitCode, 'コマンドの実行に失敗しました。');
    }
}
