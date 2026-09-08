<?php

namespace Tests\Unit\DB\Shipt;

use App\Enum\CardCondition;
use App\Enum\CardLanguage;
use App\Enum\ShiptMethod;
use App\Enum\SortOrder;
use App\Http\Controllers\ShiptLogController;
use App\Models\Shipping;
use App\Models\Shipt\OrderItem;
use App\Models\Shipt\Orders;
use App\Models\Stockpile;
use App\Services\Constant\GlobalConstant as GC;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\Database\Seeders\DatabaseSeeder;
use Tests\Database\Seeders\Shipt\TestOrderSeeder;
use Tests\Database\Seeders\TestCardInfoSeeder;
use Tests\Database\Seeders\TestStockpileSeeder;
use Tests\Database\Seeders\TruncateAllTables;
use Tests\TestCase;
use App\Services\Constant\ShiptConstant as SC;
use App\Services\Constant\CardConstant as CC;
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
                GC::ID => $order->id,
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
            SC::FEE.'.'.GC::ID => $fee->id,
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
            GC::ID => $order->id,
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
                                $query->orderBy(GC::ID, SortOrder::ASC->value);
                            }
                        ])->inRandomOrder()->first();
        $response = $this->show($orders->id);
        $response->assertJson(function (AssertableJson $json) use ($orders) {
            $json->has(SC::ITEMS, $orders->item_count)->etc();
            foreach ($orders->orderitems as $index => $expected) {
                $json->whereAll([
                    SC::ITEMS.'.'.$index.'.'.GC::ID => $expected->id,
                    SC::ITEMS.'.'.$index.'.'.SC::SHIPMENT => $expected->quantity,
                    SC::ITEMS.'.'.$index.'.'.SC::UNIT_PRICE => $expected->unit_price,
                    SC::ITEMS.'.'.$index.'.'.SC::SUBTOTAL => $expected->subtotal,
                ])->etc();
            }
        });
    }

    #[Test]
    #[TestWith([CardLanguage::JP], '日本語')]
    #[TestWith([CardLanguage::EN], '英語')]
    #[TestWith([CardLanguage::CT], '繁体中国語')]
    #[TestWith([CardLanguage::CS], '簡体中国語')]
    #[TestWith([CardLanguage::IT], 'イタリア語')]
    #[TestDox('商品情報の言語表示について検証する。')]
    public function 商品情報_言語(CardLanguage $lang) {
        $item = OrderItem::whereHas('stockpile', function ($query) use ($lang) {
            $query->where('language', $lang->value);
        })->first();
        $this->assertNotNull($item->stockpile, '指定した言語の在庫情報が存在しません。言語: '.$lang->value);
        $this->assertEquals($lang->value, $item->stockpile->language, '在庫情報の言語が一致しません。注文明細ID: '.$item->id);
        $response = $this->show($item->order_id);
        logger()->info('注文明細ID: '.$item->id.'、在庫情報ID: '.$item->stock_id.'、言語: '.$lang->value);

        $responseItems = $response->json(SC::ITEMS);
        $this->assertTrue(
            collect($responseItems)->contains(function ($ritem) use ($item, $lang) {
                return $ritem[GC::ID] === $item->id
                            && $ritem[SC::STOCK][GC::ID] === $item->stock_id
                            && $ritem[SC::STOCK][StockpileHeader::LANG] === $lang->value;
            }),
            'レスポンスに指定した言語の在庫情報が含まれていません。'
        );
    }

    #[Test]
    #[TestWith([CardCondition::NM], 'NM')]
    #[TestWith([CardCondition::NM_MINUS], 'NM-')]
    #[TestWith([CardCondition::EX_PLUS], 'EX+')]
    #[TestWith([CardCondition::EX], 'EX')]
    #[TestWith([CardCondition::PLD], 'PLD')]
    #[TestDox('商品情報の状態について検証する。')]
    public function 商品情報_状態(CardCondition $cond) {
        $item = OrderItem::whereHas('stockpile', function ($query) use ($cond) {
            $query->where('condition', $cond->value);
        })->first();
        $this->assertNotNull($item->stockpile, '指定した状態の在庫情報が存在しません。状態: '.$cond->value);
        $this->assertEquals($cond->value, $item->stockpile->condition, '在庫情報の状態が一致しません。注文明細ID: '.$item->id);
        $response = $this->show($item->order_id);
        logger()->info('注文明細ID: '.$item->id.'、在庫情報ID: '.$item->stock_id.'、状態: '.$cond->value);

        $responseItems = $response->json(SC::ITEMS);
        $this->assertTrue(
            collect($responseItems)->contains(function ($ritem) use ($item, $cond) {
                return $ritem[GC::ID] === $item->id
                            && $ritem[SC::STOCK][GC::ID] === $item->stock_id
                            && $ritem[SC::STOCK][StockpileHeader::CONDITION] === $cond->value;
            }),
            'レスポンスに指定した状態の在庫情報が含まれていません。'
        );
    }


    #[Test]
    #[TestDox('商品情報内のカード情報について検証する。')]
    public function カード情報() {
        $item = OrderItem::inRandomOrder()->first();

        $this->assertNotNull($item->stockpile, '指定した状態の在庫情報が存在しません。');
        $this->assertNotNull($item->stockpile->cardinfo, 'カード情報が存在しません。注文明細ID: '.$item->id);
        $response = $this->show($item->order_id);
        $responseItems = $response->json(SC::ITEMS);
        $this->assertTrue(
            collect($responseItems)->contains(function ($ritem) use ($item) {
                $card = $item->stockpile->cardinfo;
                $actual = $ritem[SC::STOCK][CC::CARD];
                return $ritem[GC::ID] === $item->id
                            && $actual[GC::ID] === $card->id
                            && $actual[GC::NAME] === $card->name
                            && $actual[CC::EXP][GC::NAME] === $card->expansion->name
                            && $actual[CC::EXP][CC::ATTR] === $card->expansion->attr
                            && $actual[CC::NUMBER] === $card->number
                            && $actual[CC::IMAGE_URL] === $card->image_url;
            }),
            '指定したカード情報がレスポンスに含まれていません。'
        );
    }

    #[Test]
    #[TestWith([1], '通常版')]
    #[TestWith([2], 'Foil版')]
    #[TestWith([99], '特殊Foil版')]
    #[TestDox('カード情報のfoil要素の表示について検証する')]
    public function カード情報_foiltype(int $foiltype_id) {
        $item = null;
        if ($foiltype_id === 99) {
            $item = OrderItem::whereHas('stockpile.cardinfo', function ($query) {
                $query->whereNotIn(CC::FOIL_ID, [1, 2]);
            })->first();
        } else {
            $item = OrderItem::whereHas('stockpile.cardinfo', function ($query) use($foiltype_id) {
                $query->where(CC::FOIL_ID, $foiltype_id);
            })->first();
            $this->assertEquals($foiltype_id, $item->stockpile->cardinfo->foiltype->id, '在庫情報のfoiltype_idが一致しません。注文明細ID: '.$item->id);
        }

        $this->assertNotNull($item->stockpile, '指定した状態の在庫情報が存在しません。');

        $response = $this->show($item->order_id);
        $responseItems = $response->json(SC::ITEMS);
        $this->assertTrue(
            collect($responseItems)->contains(function ($ritem) use ($item) {
                $card = $item->stockpile->cardinfo;
                $actual = $ritem[SC::STOCK][CC::CARD][CC::FOIL];
                return $ritem[GC::ID] === $item->id
                            && $actual['is_foil'] === $card->isFoil
                            && $actual[GC::NAME] === $card->foiltype->name;
            }),
            '指定したfoiltypeのカード情報がレスポンスに含まれていません。'
        );
    }

    #[Test]
    #[TestWith([false], '通常版')]
    #[TestWith([true], '特別版')]
    #[TestDox('カード情報のpromotype要素の表示について検証する')]
    public function カード情報_promotype(bool $isPromo) {
        $item = null;
        if ($isPromo) {
            $item = OrderItem::whereHas('stockpile.cardinfo', function ($query) {
                $query->whereNot(CC::PROMO_ID, 1);
            })->first();
        } else {
            $item = OrderItem::whereHas('stockpile.cardinfo', function ($query) {
                $query->where(CC::PROMO_ID, 1);
            })->first();
        }

        $this->assertNotNull($item->stockpile, '指定した状態の在庫情報が存在しません。');
        $this->assertNotNull($item->stockpile->cardinfo, 'カード情報が存在しません。注文明細ID: '.$item->id);
        $response = $this->show($item->order_id);
        $responseItems = $response->json(SC::ITEMS);
        $this->assertTrue(
            collect($responseItems)->contains(function ($ritem) use ($item) {
                $card = $item->stockpile->cardinfo;
                $actual = $ritem[SC::STOCK][CC::CARD][CC::PROMOTYPE];
                return $ritem[GC::ID] === $item->id
                            && $actual[GC::ID] === $card->promotype_id
                            && $actual[GC::NAME] === $card->promotype->name;
            }),
            '指定したpromotypeのカード情報がレスポンスに含まれていません。'
        );
    }

    #[Test]
    #[TestDox('注文情報が存在しない場合、404エラーが返ることを検証する。')]
    public function 注文情報が存在しない() {
        $response = $this->get('/api/shipping/99999');
        $response->assertNotFound();
        $response->assertJson(function (AssertableJson $json) {
            $json->whereAll([
                'title' => '情報なし',
                'status' => 404,
                'detail' => '指定した情報がありません。'])
                ->etc();
        });
    }
    // IDが数字以外
    #[Test]
    #[TestDox('注文情報IDが数字以外の場合、400エラーが返ることを検証する。')]
    public function 注文情報IDが数字以外() {
        $response = $this->get('/api/shipping/abc');
        $response->assertBadRequest();
        $response->assertJson(function (AssertableJson $json) {
        $json->whereAll([
                'title' => 'Validation Error',
                'status' => 400,
                'detail' => 'IDは数字で入力してください。'])
                ->etc();
        });
    }
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
