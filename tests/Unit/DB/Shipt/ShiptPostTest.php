<?php

namespace Tests\Unit\DB\Shipt;

use App\Http\Controllers\ShiptLogController;
use App\Models\ShippingLog;
use App\Models\Stockpile;
use App\Services\CardBoardService;
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
use Carbon\CarbonImmutable;
use FiveamCode\LaravelNotionApi\Entities\Page;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\Util\TestDateUtil;

/**
 * 出荷情報登録のテストクラス
 *
 */
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
    #[TestWith([''], '未入力')]
    #[TestDox('発送日がどの日付でも登録できることを検証する')]
    public function ok_shippingDate(string $date): void{
        $request = ShiptLogTestHelper::createStoreRequest();
        $request[SC::SHIPPING_DATE] = ShiptLogTestHelper::getShiptDate($date);
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
    #[TestDox('不正な商品情報がある場合、行数とメッセージが返ってくるか検証する。')]
    #[TestWith([GC::ID, '9999', 'no-info'], '在庫情報が存在しない')]
    // #[TestWith([SC::ORDER_ID, 'error', 'no-notion'], '注文番号が入力されたNotionカードが存在しない')]
    // #[TestWith([SC::QUANTITY, '999', 'excess-shipment'], '出荷枚数が在庫枚数より多い')]
    // #[TestWith([GC::ID, '3', 'zero_quantity'], '在庫枚数が無い')]
    public function testNgItemError(string $key, string $value, string $msg) {
        $request = ShiptLogTestHelper::createStoreRequest(2);
        // 2件目の商品情報を不正にする
        $request[SC::ITEMS][1][$key] = $value;

        $response = $this->post('api/shipping', $request);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        // JSONレスポンスの検証
        $response->assertJson(function (AssertableJson $json) use ($msg) {
            $json->hasAll(['errors']);
            $json->where('errors.1.message', "商品情報エラー: {$msg}");
        });
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
        $response->assertStatus(Response::HTTP_CREATED);
        // JSONレスポンスの検証
        $response->assertJson(function (AssertableJson $json) use ($orderId) {
            $json->hasAll([SC::ORDER_ID, GC::CREATE_AT]);

            $lastLog = ShippingLog::fetchLatestLog($orderId);
            $expected = TestDateUtil::formatDateTime($lastLog->created_at);
            $json->whereAll([SC::ORDER_ID => $orderId, GC::CREATE_AT => $expected]);
        });

        $count = ShippingLog::where(SC::ORDER_ID, $orderId)->count();

        $itemCount = count($request[SC::ITEMS]);
        $this->assertEquals($itemCount, $count, "出荷情報の登録件数を検証する。");

        $orderId = $request[SC::ORDER_ID];

        if (empty($request[SC::SHIPPING_DATE])) {
            $request[SC::SHIPPING_DATE] = TestDateUtil::formatToday();
        }

        foreach ($request[SC::ITEMS] as $item) {
            $this->assertDatabaseHas(ShippingLog::class, [
                SC::ORDER_ID => $orderId,
                GC::NAME => $request[SC::BUYER],
                SC::ZIPCODE => $request[SC::ZIPCODE],
                SC::ADDRESS => $request[SC::ADDRESS],
                SC::SHIPPING_DATE => $request[SC::SHIPPING_DATE],
                SC::STOCK_ID => $item[GC::ID],
                StockpileHeader::QUANTITY => $item[SC::SHIPMENT],
                SC::SINGLE_PRICE => $item[SC::SINGLE_PRICE],
                SC::TOTAL_PRICE => $item[SC::TOTAL_PRICE],
            ]);

            $expected = array_filter($beforeStockpile, function($before) use ($item) {
                if ($before[GC::ID] === $item[GC::ID]) {
                    return $before;
                }
            });
            $exp = \current($expected);
            $this->assertDatabaseHas(Stockpile::class, [
                GC::ID => $item[GC::ID],
                StockpileHeader::QUANTITY => $exp[StockpileHeader::QUANTITY] - $item[SC::SHIPMENT],
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
