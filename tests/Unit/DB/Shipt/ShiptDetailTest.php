<?php

namespace Tests\Unit\DB\Shipt;

use App\Enum\CardCondition;
use App\Enum\CardLanguage;
use App\Enum\ShiptMethod;
use App\Enum\SortOrder;
use App\Http\Controllers\ShiptLogController;
use App\Models\Shipping;
use App\Models\Shipt\Orders;
use App\Models\Stockpile;
use App\Services\Constant\GlobalConstant;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\Database\Seeders\DatabaseSeeder;
use Tests\Database\Seeders\Shipt\TestOrderSeeder;
use Tests\Database\Seeders\TestCardInfoSeeder;
use Tests\Database\Seeders\TestStockpileSeeder;
use Tests\Database\Seeders\TruncateAllTables;
use Tests\TestCase;
use App\Services\Constant\ShiptConstant as SC;
use App\Services\Constant\StockpileHeader;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;

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

    #[Test]
    #[TestDox('顧客情報と金額情報を検証する。')]
    public function 顧客情報と金額情報(): void
    {
        $order = Orders::inRandomOrder()->first();
        $condition = [
                GlobalConstant::ID => $order->id,
                SC::PLATFORM => $order->platform,
                SC::PLATFORM_ORDER_ID => $order->platform_order_id,
                SC::ZIPCODE => $order->zip_code,
                SC::ADDRESS => $order->address,
                SC::BUYER => $order->buyer_name,
                SC::ITEM_COUNT => $order->item_count,
                SC::ITEM_SUBTOTAL => $order->items_subtotal,
                SC::GRAND_TOTAL => $order->grand_total,
        ];
        $this->verifyDetailInfo($order, $condition);
    }

    #[Test]
    #[TestDox('発送日について検証する。')]
    public function 発送日() {
        $order = Orders::inRandomOrder()->first();
        $response = $this->show($order->id);
        $response->assertJson(function(AssertableJson $json) use ($order) {
            // 発送日の形式チェック
            $json->whereType(SC::SHIPPING_DATE, 'string')
                ->where(SC::SHIPPING_DATE, $order->shipt_date)
                ->where(SC::SHIPPING_DATE, function ($value) {
                    // yyyy/MM/dd にマッチする正規表現
                    return preg_match('/^\d{4}\/\d{2}\/\d{2}$/', $value) === 1;
                })
                ->etc();
        });
    }

    #[Test]
    #[TestDox('送料の表示について検証する。')]
    #[TestWith([ShiptMethod::MINI], 'ミニレター')]
    #[TestWith([ShiptMethod::CLICK], 'クリックポスト')]
    #[TestWith([ShiptMethod::REGISTER_MAIL], '簡易書留')]
    public function 送料(ShiptMethod $method) {
        $fee = Shipping::findByMethod($method->value);
        $order = Orders::where(SC::FEE_ID, $fee->id)->inRandomOrder()->first();
        $condition = [
            SC::FEE.'.'.GlobalConstant::ID => $fee->id,
            SC::FEE.'.'.SC::METHOD => $fee->name,
            SC::FEE.'.'.SC::PRICE => $fee->price,
        ];
        $this->verifyDetailInfo($order, $condition);
    }

    #[Test]
    #[TestDox('前後の注文情報IDが取得できているか検証する。')]
    #[TestWith([1, 0, 2], '最初のレコード')]
    #[TestWith([7, 6, 8], '真ん中のレコード')]
    #[TestWith([15, 14, 0], '最後のレコード')]
    public function 隣接した注文情報ID(int $orderId, int $expectedPreviousId, int $expectedNextId) {
        $order = Orders::find($orderId);
        $condition = [
            GlobalConstant::ID => $order->id,
            SC::PREV_ID => $expectedPreviousId,
            SC::NEXT_ID => $expectedNextId,
        ];
        $this->verifyDetailInfo($order, $condition);
    }

    #[Test]
    #[TestDox('注文明細のID、枚数、単価、小計の情報を検証する。')]
    public function 注文明細_商品情報以外() {
        $orders = Orders::whereNot(SC::ITEM_COUNT, '=', 1)
                            ->with([
                            'orderitems' => function ($query) {
                                $query->orderBy(GlobalConstant::ID, SortOrder::ASC->value);
                            }
                        ])->inRandomOrder()->first();
        $response = $this->show($orders->id);
        $response->assertJson(function (AssertableJson $json) use ($orders) {
            $json->has(SC::ITEMS, $orders->item_count)->etc();
            foreach ($orders->orderitems as $index => $expected) {
                $json->whereAll([
                    SC::ITEMS.'.'.$index.'.'.GlobalConstant::ID => $expected->id,
                    SC::ITEMS.'.'.$index.'.'.SC::SHIPMENT => $expected->quantity,
                    SC::ITEMS.'.'.$index.'.'.SC::UNIT_PRICE => $expected->unit_price,
                    SC::ITEMS.'.'.$index.'.'.SC::SUBTOTAL => $expected->subtotal,
                ])->etc();
            }
        });
    }

    #[Test]
    #[TestWith([CardLanguage::JP, CardCondition::UNDEFINED], '言語_日本語')]
    #[TestWith([CardLanguage::EN, CardCondition::UNDEFINED], '言語_英語')]
    #[TestWith([CardLanguage::CT, CardCondition::UNDEFINED], '言語_繁体中国語')]
    #[TestWith([CardLanguage::CS, CardCondition::UNDEFINED], '言語_簡体中国語')]
    #[TestWith([CardLanguage::IT, CardCondition::UNDEFINED], '言語_イタリア語')]
    #[TestWith([CardLanguage::UNDEFINED, CardCondition::NM], '状態_NM')]
    #[TestWith([CardLanguage::UNDEFINED, CardCondition::NM_MINUS], '状態_NM-')]
    #[TestWith([CardLanguage::UNDEFINED, CardCondition::EX_PLUS], '状態_EX+')]
    #[TestWith([CardLanguage::UNDEFINED, CardCondition::EX], '状態_EX')]
    #[TestWith([CardLanguage::UNDEFINED, CardCondition::PLD], '状態_PLD')]
    #[TestDox('注文明細の商品情報について検証する。')]
    public function 注文明細_商品情報(CardLanguage $lang, CardCondition $condition) {
        $orders = Orders::with([
                                'orderitems.stockpile' => function ($query) use ($lang, $condition) {
                                        $query->when($lang !== CardLanguage::UNDEFINED, function($query) use ($lang) {
                                            return $query->where(StockpileHeader::LANGUAGE, $lang->value);
                                        })
                                        ->when($condition !== CardCondition::UNDEFINED, function($query) use ($condition) {
                                            return $query->where(StockpileHeader::CONDITION, $condition->value);
                                        });
                                    },
                            'orderitems' => function ($query) {
                                $query->orderBy(GlobalConstant::ID, SortOrder::ASC->value);
                            }
                        ])->inRandomOrder()->first();
        $this->assertJsonWhereAll($orders, SC::ITEMS, SC::STOCK, function ($item) {
            $stock = Stockpile::findById($item->stock_id);
            $this->assertNotNull($stock, '在庫情報が存在しません。注文明細ID: '.$item->id);
            return [
                GlobalConstant::ID => $stock->id,
                StockpileHeader::LANG => $stock->language,
                StockpileHeader::CONDITION => $stock->condition,
                StockpileHeader::QUANTITY => $stock->quantity,
            ];
        });
    }

    // 在庫情報_状態
    // 在庫情報_言語
    // 通常版
    // Non-foil版
    // Foil版
    // Promo版

    // エラー_注文情報が存在しない
    // IDが数字以外

    /**
     * 詳細情報を取得する。
     *
     * @param Orders $order
     * @param array $condition
     * @return void
     */
    private function verifyDetailInfo(Orders $order, array $condition) {
        $this->assertNotNull($order, '期待値側の注文情報がありません');
        $response = $this->show($order->id);
        $response->assertJson(function(AssertableJson $json) use ($order, $condition) {
            return $json->whereAll($condition)->etc();
        });
    }

    private function assertJsonWhereAll(Orders $order, string $collectionKey, string $targetKey,
                                                                                                                                                        callable $conditions): void {
        $this->assertNotNull($order, '期待値側の注文情報がありません');
        $response = $this->show($order->id);
        $items = $order->orderitems;
        $response->assertJson(function (AssertableJson $json) use (
            $collectionKey, $items, $targetKey, $conditions) {
            $json->has($collectionKey, $items->count())->etc();

            foreach ($items as $index => $item) {
                $key = $collectionKey.'.'.$index.'.'.$targetKey;

                $json->has($key)->etc();

                $json->dump()->whereAll(
                    collect($conditions($item))
                        ->mapWithKeys(fn ($value, $field) => [
                            $key.'.'.$field => $value,
                        ])
                        ->all()
                )->etc();
            }
        });
    }

    /**
     * 詳細情報を取得する。
     *
     * @param integer $id
     * @return TestResponse
     */
    private function show(int $id) {
        $response = $this->get('/api/shipping/'.$id);
        $response->assertOk();
        return $response;
    }
}
