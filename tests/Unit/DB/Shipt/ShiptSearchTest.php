<?php
namespace Tests\Unit\DB\Shipt;

use App\Enum\SortOrder;
use App\Http\Controllers\ShiptLogController;
use App\Models\Shipping;
use App\Models\Shipt\Orders;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;
use Tests\Trait\GetApiAssertions;
use App\Services\Constant\GlobalConstant as GC;
use App\Services\Constant\ShiptConstant as SC;
use Illuminate\Http\Response;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
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
    #[TestWith([[SC::SHIPT_DATE]], '発送日を指定')]
    #[TestWith([[SC::BUYER]], '購入者名を指定')]
    #[TestWith([[SC::SHIPT_DATE, SC::BUYER]], '両方を指定')]
    public function ok(array $keys)
    {
        $order = Orders::inRandomOrder()->first();
        $condition = [];
        $query = Orders::query()->orderBy(GC::ID, SortOrder::ASC->value);
        if (in_array(SC::SHIPT_DATE, $keys)) {
            $condition[SC::SHIPT_DATE] = $order->shipt_date;
            $query = $query->where(SC::SHIPT_DATE, $order->shipt_date);
        }
        if (in_array(SC::BUYER, $keys)) {
            $condition[SC::BUYER] = $order->buyer_name;
            $query = $query->where(SC::BUYER, $order->buyer_name);
        }
        $this->assertNotSame(count($condition), 0);

        $exOrders = $query->get();
        $response = $this->assert_OK($condition);
        $response->assertJsonCount($exOrders->count());
        for($i = 0; $i < $exOrders->count(); $i++) {
            $ex = $exOrders[$i];
            $fee = Shipping::find($ex->shipt_fee_id);
            // 検索結果の確認
            $response->assertJson(function(AssertableJson $json) use($i, $ex, $fee) {
                $json->whereAll([
                    "{$i}.". GC::ID => $ex->id,
                    "{$i}.". SC::PLATFORM => $ex->platform,
                    "{$i}.". SC::PLATFORM_ORDER_ID => $ex->platform_order_id,
                    "{$i}.". SC::BUYER => $ex->buyer_name,
                    "{$i}.". SC::ZIPCODE => $ex->zip_code,
                    "{$i}.". SC::ADDRESS => $ex->address,
                    "{$i}.". SC::ITEM_COUNT => $ex->item_count,
                    "{$i}.". SC::SHIPT_DATE => $ex->shipt_date,
                    "{$i}.". SC::ITEM_SUBTOTAL => $ex->items_subtotal,
                    "{$i}.". SC::GRAND_TOTAL => $ex->grand_total,
                    "{$i}.". SC::DISCOUNT_AMOUNT => $ex->coupon_discount,
                    "{$i}.". SC::FEE.".".GC::ID => $fee->id,
                    "{$i}.". SC::FEE.".".SC::METHOD => $fee->name,
                    "{$i}.". SC::FEE.".".SC::PRICE => $fee->price,
                    ]);
                $json->missingAll([SC::PREV_ID, SC::NEXT_ID, SC::ITEMS])->etc();
            });
        }
    }

    #[Test]
    #[TestDox('検索結果が無い場合、エラーが返ってくるか検証する')]
    public function ngNotFound() {
        $this->assert_NG([SC::BUYER => 'zzzz'], Response::HTTP_NOT_FOUND, '検索結果がありません。');
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
