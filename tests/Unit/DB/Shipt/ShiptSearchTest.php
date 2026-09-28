<?php
namespace Tests\Unit\DB\Shipt;

use App\Enum\SortOrder;
use App\Http\Controllers\ShiptLogController;
use App\Models\Shipt\Orders;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;
use Tests\Trait\GetApiAssertions;
use App\Services\Constant\GlobalConstant as GC;
use App\Services\Constant\ShiptConstant as SC;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\Test;
use Tests\Database\Seeders\DatabaseSeeder;
use Tests\Database\Seeders\Shipt\TestOrderSeeder;
use Tests\Database\Seeders\TestCardInfoSeeder;
use Tests\Database\Seeders\TestStockpileSeeder;
use Tests\Database\Seeders\TruncateAllTables;
use Tests\Util\TestDateUtil;

#[TestDox('注文情報検索機能のテスト')]
#[CoversClass(ShiptLogController::class)]
class ShiptSearchTest extends TestCase
{
    use GetApiAssertions;

    public function setup():void {
        parent::setup();
        $this->seed(TruncateAllTables::class);
        $this->seed(DatabaseSeeder::class);
        $this->seed(TestCardInfoSeeder::class);
        $this->seed(TestStockpileSeeder::class);
        $this->seed(TestOrderSeeder::class);
    }

    #[Test]
    #[TestDox('日付を指定した検索を検証する')]
    public function ok()
    {
        $order = Orders::inRandomOrder()->first();
        $date = $order->shipt_date;
        $exOrders = Orders::where(SC::SHIPT_DATE, $date)
                                                ->orderBy(GC::ID, SortOrder::ASC->value)->get();
        $response = $this->assert_OK([SC::SHIPT_DATE => $date]);
        $response->assertJsonCount($exOrders->count());
        for($i = 0; $i < $exOrders->count(); $i++) {
            $ex = $exOrders[$i];
            // 検索結果の確認
            $response->assertJson(function(AssertableJson $json) use($i, $ex) {
                $json->whereAll([
                    "{$i}.". GC::ID => $ex->id,
                    "{$i}.". SC::PLATFORM => $ex->platform,
                    "{$i}.". SC::PLATFORM_ORDER_ID => $ex->platform_order_id,
                    "{$i}.". SC::BUYER => $ex->buyer_name,
                    "{$i}.". SC::ZIPCODE => $ex->zip_code,
                    "{$i}.". SC::ADDRESS => $ex->address,
                    "{$i}.". SC::ITEM_COUNT => $ex->item_count
                    ])->etc();
                $json->missingAll([SC::PREV_ID, SC::NEXT_ID, SC::ITEMS])->etc();
            });
        }
    }

    /**
     * エンドポイントを取得する。
     *
     * @return string
     */
    protected function getEndPoint():string {
        return  'api/shipping';
    }
}
