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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

#[TestDox('divide:orderコマンドのテスト')]
#[CoversClass(App\Console\Commands\DivideOrder::class)]
class DivideOrderTest extends TestCase
{

    private $cal2024 = '2024-10-01';

    private $cal2026 = '2026-10-01';

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
    #[TestWith([ShopPlatform::MERCARI], 'メルカリShops')]
    #[TestWith([ShopPlatform::DIRECT], '直接取引')]
    #[TestWith([ShopPlatform::BASE], 'BASEショップ')]
    public function platform(ShopPlatform $platform) {
        $this->execute();
        $query = $this->fetchByShopQuery($platform);
        $uniqueLog = $this->fetchExRecord($query);
        foreach($uniqueLog as $u) {
            $this->assertDatabaseHas(Orders::class, [
                SC::PLATFORM => $platform->value,
                SC::PLATFORM_ORDER_ID => $u->order_id,
            ]);
        }
    }

    #[Test]
    #[TestDox('配送方法が「手渡し」に関する判定について検証する。')]
    public function fee_direct() {
        $query = $this->fetchByShopQuery(ShopPlatform::DIRECT);
        $this->verifyFee(ShopPlatform::DIRECT, ShiptMethod::DIRECT, false, $query);
    }

    #[Test]
    #[TestDox('らくらくメルカリ便について検証する')]
    public function fee_rakuraku() {
        $query = $this->fetchByShopQuery(ShopPlatform::MERCARI)->where(SC::ZIPCODE, '100-0000');
        $this->verifyFee(ShopPlatform::MERCARI, ShiptMethod::RAKURAKU, false, $query);
    }

    #[Test]
    #[TestDox('簡易書留について検証する。')]
    public function fee_registerMail() {
        $query = $this->fetchByShopQuery(ShopPlatform::MERCARI);
        $result = $query->select('order_id', DB::raw('SUM(total_price) as total_price'))
            ->groupBy('order_id')->havingRaw('SUM(total_price) = ?', [11000 - 460])->get();
        $this->verifyFeeRecord(ShopPlatform::MERCARI, ShiptMethod::REGISTER_MAIL, false, $result);
    }

    #[Test]
    #[TestDox('メルカリ経由のクリックポストについて検証する。')]
    #[TestWith([1, true, 1500], '発送日が2026/10/1より前で、価格¥1500、商品数1件')]
    #[TestWith([2, true, 1499], '発送日が2026/10/1より前で、価格¥1500、商品数2件')]
    #[TestWith([1, true, 9999], '発送日が2026/10/1より前で、価格¥9999、商品数1件')]
    #[TestWith([2, true, 9998], '発送日が2026/10/1より前で、価格¥9999、商品数2件')]
    #[TestWith([1, false, 1500], '発送日が2026/10/1以降で、価格¥1500、商品数1件')]
    #[TestWith([2, false, 1500], '発送日が2026/10/1以降で、価格¥1500、商品数2件')]
    #[TestWith([1, false, 9999], '発送日が2026/10/1以降で、価格¥9999、商品数1件')]
    #[TestWith([2, false, 9999], '発送日が2026/10/1以降で、価格¥9999、商品数2件')]
    public function fee_clickpost(int $count, bool $isBefore, int $total) {
        $this->verifyPostOffice(ShopPlatform::MERCARI, ShiptMethod::CLICK, $count, $isBefore, $total, $this->cal2026);
    }

    #[Test]
    #[TestDox('メルカリ経由のミニレターについて検証する。')]
    #[TestWith([1, true, 300], '発送日が2024/10/1より前で、価格¥300、商品数1件')]
    #[TestWith([2, true, 299], '発送日が2024/10/1より前で、価格¥300、商品数2件')]
    #[TestWith([1, true, 1499], '発送日が2024/10/1より前で、価格¥1499、商品数1件')]
    #[TestWith([2, true, 1498], '発送日が2024/10/1より前で、価格¥1499、商品数2件')]
    #[TestWith([1, false, 300], '発送日が2024/10/1以降で、価格¥300、商品数1件')]
    #[TestWith([2, false, 299], '発送日が2024/10/1以降で、価格¥300、商品数2件')]
    #[TestWith([1, false, 1499], '発送日が2024/10/1以降で、価格¥1499、商品数1件')]
    #[TestWith([2, false, 1498], '発送日が2024/10/1以降で、価格¥1499、商品数2件')]
    public function fee_miniletter_mercari(int $count, bool $isBefore, int $total) {
        $this->verifyPostOffice(ShopPlatform::MERCARI, ShiptMethod::MINI, $count, $isBefore, $total, $this->cal2024);
    }

    #[Test]
    #[TestDox('BASE経由のミニレターについて検証する。')]
    #[TestWith([1, true, 113], '発送日が2024/10/1より前で、価格¥113、商品数1件')]
    #[TestWith([1, true, 4999], '発送日が2024/10/1より前で、価格¥4999、商品数1件')]
    #[TestWith([2, true, 4998], '発送日が2024/10/1より前で、価格¥4999、商品数2件')]
    #[TestWith([1, false, 135], '発送日が2024/10/1以降で、価格¥135、商品数1件')]
    #[TestWith([1, false, 4999], '発送日が2024/10/1以降で、価格¥4999、商品数1件')]
    #[TestWith([2, false, 4998], '発送日が2024/10/1以降で、価格¥4999、商品数2件')]
    public function fee_miniletter_base( int $count, bool $isBefore, int $total) {
        $this->verifyPostOffice(ShopPlatform::BASE, ShiptMethod::MINI, $count, $isBefore, $total, $this->cal2024);
    }


    /**
     * ミニレターとクリックポストについて検証する。
     *
     * @param ShopPlatform $platform
     * @param ShiptMethod $method
     * @param integer $count
     * @param boolean $isBefore
     * @param integer $total
     * @param string $initDate
     * @return void
     */
    private function verifyPostOffice(ShopPlatform $platform, ShiptMethod $method, int $count, bool $isBefore, int $total, string $initDate) {
        $symbol = $isBefore ? '<' : '>=';
        $fee = 0;
        if ($method === ShiptMethod::MINI) {
            $fee = $isBefore ? 63 : 85;
        } else {
            $fee = $isBefore ? 185 : 240;
        }
        $exPrice = $total - $fee;
        $query = $this->fetchByShopQuery($platform)->whereDate('shipping_date', $symbol, $initDate);
        $result = $query->select('order_id', DB::raw('SUM(total_price) as total_price'), DB::raw('COUNT(order_id) as order_count'))
            ->groupBy('order_id')->havingRaw("SUM(total_price) = {$exPrice}")->havingRaw("COUNT(order_id) = {$count}")->get();
        $this->verifyFeeRecord($platform, $method, $isBefore, $result);
    }

    /**
     * 配送方法について検証する。
     *
     * @param ShopPlatform $platform
     * @param ShiptMethod $method
     * @param boolean $isDeleted
     * @param Builder $query
     * @return void
     */
    private function verifyFee(ShopPlatform $platform, ShiptMethod $method, bool $isDeleted, Builder $query) {
        $this->execute();
        $uniqueLog = $this->fetchExRecord($query);
        $this->verifyFeeRecord($platform, $method, $isDeleted, $uniqueLog);
    }

    private function verifyFeeRecord(ShopPlatform $platform, ShiptMethod $method, bool $isDeleted, Collection $uniqueLog) {
        $this->execute();
        $expected = $this->shipping($method, $isDeleted);
        $this->assertCount(1, $uniqueLog, '期待値側が0件');
        foreach($uniqueLog as $u) {
            logger()->info("期待値側のID：{$u->order_id}");
            $this->assertDatabaseHas(Orders::class, [
                SC::PLATFORM => $platform->value,
                SC::PLATFORM_ORDER_ID => $u->order_id,
                SC::FEE_ID => $expected->id,
            ]);
        }
    }

    private function shipping(ShiptMethod $method, bool $isDeleted) {
        $shipping = Shipping::when($isDeleted, function($query) {
            return $query->inactive();
        }, function($query) {
            return $query->active();
        })->where(GC::NAME, $method->value)->sole();

        return $shipping;
    }

    private function fetchByShopQuery(ShopPlatform $platform)
    {
        if ($platform === ShopPlatform::BASE) {
            $query = ShippingLog::where('order_id', 'not like', 'order_%')->where('order_id', 'not like', 'd2c_%');
        } else {
            $query = ShippingLog::whereLike('order_id', "{$platform->orderPrefix()}_%");
        }
        return $query;
    }

    private function fetchExRecord(Builder $query) {
        $log = $query->orderBy('shipping_date', SortOrder::ASC->value)->get();
        $uniqueLog = $log->unique('order_id');
        return $uniqueLog;
    }

    /**
     * divide:orderコマンドを実行する。
     *
     * @return void
     */
    private function execute() {
        $exitCode  = Artisan::call('divide:order');
        logger()->info(Artisan::output());
        $this->assertEquals(0, $exitCode, 'コマンドの実行に失敗しました。');
    }
}
