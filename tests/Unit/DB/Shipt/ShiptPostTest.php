<?php

namespace Tests\Unit\DB\Shipt;

use App\Enum\SortOrder;
use App\Http\Controllers\ShiptLogController;
use App\Models\ShippingLog;
use App\Models\Shipt\OrderItem;
use App\Models\Shipt\Orders;
use App\Models\Stockpile;
use App\Services\CardBoardService;
use App\Services\Constant\ErrorConstant as EC;
use App\Services\Constant\GlobalConstant as GC;
use Illuminate\Http\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\Database\Seeders\DatabaseSeeder;
use Tests\Database\Seeders\TestCardInfoSeeder;
use Tests\Database\Seeders\TestStockpileSeeder;
use Tests\Database\Seeders\TruncateAllTables;
use Tests\TestCase;
use App\Services\Constant\ShiptConstant as SC;
use App\Services\Constant\StockpileHeader;
use Database\Factories\Shipt\OrdersFactory;
use FiveamCode\LaravelNotionApi\Entities\Page;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\Util\TestDateUtil;

#[TestDox('注文情報登録機能のテスト')]
#[CoversClass(ShiptLogController::class)]
class ShiptPostTest extends TestCase
{
    public function setup():void {
        parent::setup();
        $this->seed(TruncateAllTables::class);
        $this->seed(DatabaseSeeder::class);
        $this->seed(TestCardInfoSeeder::class);
        $this->seed(TestStockpileSeeder::class);
    }

    #[Test]
    #[TestWith([1], '商品情報が1件')]
    #[TestWith([2], '商品情報が2件')]
    #[TestDox('出荷情報の登録に成功することを検証する')]
    public function ok_post(int $itemCount): void
    {
        $request = ShiptLogTestHelper::createStoreRequest($itemCount);
        $this->ok($request);
    }

    #[Test]
    #[TestWith(['td'], '今日')]
    #[TestWith(['tmr'], '明日')]
    #[TestWith(['yd'], '昨日')]
    #[TestDox('発送日がどの日付でも登録できることを検証する')]
    public function ok_shippingDate(string $date): void{
        $request = ShiptLogTestHelper::createStoreRequest();
        $request[SC::SHIPT_DATE] = ShiptLogTestHelper::getShiptDate($date);
        $this->ok($request);
    }

    #[Test]
    #[TestDox('出荷枚数が在庫枚数より少ないか同等なら登録に成功することを検証する')]
    #[TestWith(['<'], '出荷枚数 < 在庫枚数')]
    #[TestWith(['='], '出荷枚数 = 在庫枚数')]
    public function ok_shipment(string $symbol) {
        $request = ShiptLogTestHelper::createStoreRequest();
        if ($symbol == '=') {
            $item = $request[SC::ITEMS][0];
            $stockId = $item[GC::ID];
            $stock = Stockpile::find((int)$stockId);
            $item[SC::SHIPMENT] = $stock->quantity;
        }
        $this->ok($request);
    }

    #[Test]
    #[TestDox('出荷商品が全て登録済みの場合はエラーが出ることを検証する')]
    public function ng_allRegistered() {
        $request = ShiptLogTestHelper::createStoreRequest(2);
        foreach ($request[SC::ITEMS] as $key => $item) {
            $request[SC::ITEMS][$key][SC::IS_REGISTERED] = true;
        }
        $response = $this->post('api/shipping', $request);
        $response->assertBadRequest();
        $orderId = $request[SC::ORDER_ID];

        $this->assertDatabaseMissing(Orders::class, [
            SC::PLATFORM => $request[SC::PLATFORM],
            SC::PLATFORM_ORDER_ID => $orderId,
        ]);
        $this->assertDatabaseCount(OrderItem::class, 0);
        $response->assertJson(function (AssertableJson $json) use ($orderId) {
            $json->hasAll([EC::TITLE, EC::DETAIL, EC::REQUEST, GC::STATUS]);
            $json->whereAll([
                GC::STATUS => Response::HTTP_BAD_REQUEST,
                EC::TITLE =>'Validation Error',
                EC::DETAIL => "{$orderId}：注文情報が全て登録されています。",
                EC::REQUEST => 'api/shipping'
            ]);
        });
    }

    #[Test]
    #[TestDox('商品情報のうち、1件だけ登録済みフラグがtrueの商品情報が登録されないことを検証する')]
    public function ignore_isRegistered() {
        Orders::factory()->under1500()->count(1)->create()
            ->each(function (Orders $order) {
                OrderItem::factory()->count($order->item_count)->create([
                    SC::ORDER_ID => $order->id,
                ]);
        });
        $order = Orders::query()->orderBy(GC::ID, SortOrder::DESC->value)->first();
        $request = ShiptLogTestHelper::createStoreRequest(2);
        $request[SC::PLATFORM] = $order->platform;
        $request[SC::ORDER_ID] = $order->platform_order_id;
        $request[SC::BUYER] = $order->buyer_name;
        $request[SC::ZIPCODE] = $order->zip_code;
        $request[SC::ADDRESS] = $order->address;
        $request[SC::ITEM_COUNT] = $order->item_count + 1;
        $request[SC::FEE][GC::ID] = $order->shipt_fee_id;
        $request[SC::SHIPT_DATE] = TestDateUtil::formatISO8601($order->shipt_date);
        $request[SC::ITEMS][0][SC::IS_REGISTERED] = true;
        $this->ok($request);

        // $this->assertDatabaseMissing(ShippingLog::class, [
        //     SC::ORDER_ID => $request[SC::ORDER_ID],
        //     SC::NAME => $request[SC::BUYER],
        //     SC::STOCK_ID => $request[SC::ITEMS][0][GC::ID],
        //     StockpileHeader::QUANTITY => $request[SC::ITEMS][0][SC::SHIPMENT],
        // ]);
    }

    /**
     * テストを実行する。
     *
     * @param array $request
     * @return TestResponse
     */
    private function ok(array $request) {
        $orderId = $request[SC::ORDER_ID];
        $this->setMockCardBoard($orderId);

        $beforeStockpile = $this->getStockpile($request);

        $response = $this->post('api/shipping', $request);
        $response->assertCreated();
        // JSONレスポンスの検証
        $lastLog = Orders::query()->orderBy(GC::ID, SortOrder::DESC->value)->first();
        $response->assertJson(function (AssertableJson $json) use ($lastLog) {
            $expected = TestDateUtil::formatDateTime($lastLog->created_at);
            $json->hasAll([GC::ID, GC::CREATE_AT])->
                               whereAll([GC::ID => $lastLog->id, GC::CREATE_AT => $expected]);
        });

        $this->assertDatabaseHas(Orders::class, [
                GC::ID => $lastLog->id,
                SC::PLATFORM => $request[SC::PLATFORM],
                SC::PLATFORM_ORDER_ID => $orderId,
                SC::BUYER => $request[SC::BUYER],
                SC::ZIPCODE => $request[SC::ZIPCODE],
                SC::ADDRESS => $request[SC::ADDRESS],
                SC::ITEM_SUBTOTAL => $request[SC::ITEM_SUBTOTAL],
                'coupon_discount' => $request[SC::DISCOUNT_AMOUNT],
                SC::GRAND_TOTAL => $request[SC::GRAND_TOTAL],
                SC::FEE_ID => $request[SC::FEE][GC::ID],
                SC::ITEM_COUNT => $request[SC::ITEM_COUNT],
                SC::SHIPT_DATE => $request[SC::SHIPT_DATE],
        ]);

        $this->assertDatabaseCount(OrderItem::class, $request[SC::ITEM_COUNT]);

        foreach ($request[SC::ITEMS] as $item) {
            if ($item[SC::IS_REGISTERED]) {
                continue;
            }
            $this->assertDatabaseHas(OrderItem::class, [
                SC::ORDER_ID => $lastLog->id,
                SC::STOCK_ID => $item[GC::ID],
                StockpileHeader::QUANTITY => $item[SC::SHIPMENT],
                SC::UNIT_PRICE => $item[SC::UNIT_PRICE],
                SC::SUBTOTAL => $item[SC::SUBTOTAL]
            ]);

            $this->assertDatabaseHas(Stockpile::class, [
                GC::ID => $item[GC::ID],
                StockpileHeader::QUANTITY => array_reduce($beforeStockpile, function ($carry, $before) use ($item) {
                    if ($before[GC::ID] === $item[GC::ID]) {
                        return $before[StockpileHeader::QUANTITY] - $item[SC::SHIPMENT];
                    }
                    return $carry;
                }, 0)
            ]);
        }
        return $response;
    }

    /**
     * 入力値から在庫IDと在庫数を取得する。
     *
     * @param array $request
     * @return Stockpile[]
     */
    private function getStockpile(array $request){
                $stockIds = array_map(function($item) {
            return $item[GC::ID];
        }, $request[SC::ITEMS]);
        $beforeStockpile = Stockpile::select(GC::ID, StockpileHeader::QUANTITY)
                                                                ->whereIn(GC::ID, $stockIds)->get()->toArray();
        return $beforeStockpile;
    }

    private function setMockCardBoard(string $orderId) {
        $mock = \Mockery::mock(CardBoardService::class);
        // Notion更新メソッドのモック設定
        $mock->shouldReceive('updatePage')->once()
        ->with(\Mockery::type(Page::class))->andReturnTrue();

        $errorId = 'error';
        if ($orderId === $errorId) {
            $mock->shouldReceive('findByOrderId')
            ->with($errorId)
            ->andReturn(collect([]));
            return;
        }
        $page = new Page();
        $page->setId(fake()->uuid());
        $mock->shouldReceive('findByOrderId')
                ->with($orderId)
                ->andReturn(collect([$page]));

        $this->app->instance(\App\Services\CardBoardService::class, $mock);
    }
}
