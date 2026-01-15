<?php

namespace Tests\Unit\DB\Shipt;

use App\Http\Controllers\ShiptLogController;
use App\Models\ShippingLog;
use App\Services\CardBoardService;
use App\Services\Constant\GlobalConstant;
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
use Carbon\CarbonImmutable;
use FiveamCode\LaravelNotionApi\Entities\Page;
use Illuminate\Testing\Fluent\AssertableJson;
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
    #[TestDox('出荷情報の登録に成功することを検証する')]
    public function ok(): void
    {
        $request = ShiptLogTestHelper::createStoreRequest();
        $this->setMockCardBoard([$request[SC::ORDER_ID]]);
        $response = $this->post('api/shipping', $request);
        $response->assertStatus(Response::HTTP_CREATED);

        $response->assertJson(function (AssertableJson $json) use ($request) {
            $json->hasAll([SC::ORDER_ID, GlobalConstant::CREATE_AT]);

            $orderId = $request[SC::ORDER_ID];

            $lastLog = ShippingLog::fetchLatestLog($orderId);
            $expected = TestDateUtil::formatDateTime($lastLog->created_at);
            $json->whereAll([SC::ORDER_ID => $orderId, GlobalConstant::CREATE_AT => $expected]);
        });

        $this->assertDatabaseHas('shipping_log', [
            SC::ORDER_ID => $request[SC::ORDER_ID]]);
    }

    private function setMockCardBoard(array $orderIds) {
        $mock = \Mockery::mock(CardBoardService::class);
        $errorId = 'error';
        foreach($orderIds as $id) {
            if ($id === $errorId) {
                continue;
            }

            $page = new Page();
            $page->setId(fake()->uuid());
            $mock->shouldReceive('findByOrderId')
                    ->with($id)
                    ->andReturn(collect([$page]));
        }
        $mock->shouldReceive('findByOrderId')
        ->with($errorId)
        ->andReturn(collect([]));

        // Notion更新メソッドのモック設定
        $mock->shouldReceive('updatePage')->once()
        ->with(\Mockery::type(Page::class))->andReturnTrue();

        $this->app->instance(\App\Services\CardBoardService::class, $mock);
    }
}
