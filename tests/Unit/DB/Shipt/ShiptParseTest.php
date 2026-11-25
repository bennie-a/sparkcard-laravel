<?php

namespace Tests\Unit\DB\Shipt;

use App\Enum\ShiptMethod;
use App\Enum\ShopPlatform;
use App\Http\Controllers\ShiptLogController;
use App\Http\Response\CustomResponse;
use App\Models\Shipping;
use App\Models\Shipt\Orders;
use App\Models\Stockpile;
use App\Services\CardBoardService;
use App\Services\Constant\CardConstant as CC;
use App\Services\Constant\GlobalConstant as GC;
use App\Services\Constant\ShiptConstant as SC;
use App\Services\Constant\ErrorConstant as EC;
use App\Services\Constant\StockpileHeader as SH;
use App\Services\Constant\StockpileHeader;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\Unit\DB\Shipt\ShiptLogTestHelper;
use Tests\Database\Seeders\DatabaseSeeder;
use Tests\Database\Seeders\Shipt\TestOrderSeeder;
use Tests\Database\Seeders\TestCardInfoSeeder;
use Tests\Database\Seeders\TestStockpileSeeder;
use Tests\Database\Seeders\TruncateAllTables;
use Tests\TestCase;
use Tests\Trait\ApiErrorAssertions;
use Tests\Util\TestDateUtil;

/**
 * 出荷情報解析機能のテストケース
 */
#[CoversClass(ShiptLogController::class)]
#[TestDox('注文情報解析機能のテスト')]
class ShiptParseTest extends TestCase
{
    use ApiErrorAssertions;

    public function setup():void {
        parent::setup();
        $this->seed(TruncateAllTables::class);
        $this->seed(DatabaseSeeder::class);
        $this->seed(TestCardInfoSeeder::class);
        $this->seed(TestStockpileSeeder::class);
     }

     #[Test]
     #[TestDox('プラットフォームが正しく設定されているか確認する')]
     #[TestWith([ShopPlatform::MERCARI], 'メルカリ')]
     #[TestWith([ShopPlatform::BASE], 'BASE')]
     #[TestWith([ShopPlatform::DIRECT], '手渡し')]
     public function platform(ShopPlatform $platform): void {
        $buyerInfo = ShiptLogTestHelper::createBuyerInfo(1, false, false, 1, $platform);
         $response = $this->uploadOk([$buyerInfo]);
         $response->assertJsonPath('0.'.SC::PLATFORM, $platform->value);
     }

    #[TestDox('購入者情報が正しく集計されているか確認する')]
    #[TestWith([1, 1], '購入者1人_出荷商品1件')]
    #[TestWith([1, 2], '購入者1人_出荷商品2件')]
    #[TestWith([2, 1], '購入者2人_出荷商品1件')]
    #[TestWith([2, 1], '購入者2人_出荷商品2件')]
    public function testBuyerAndItemCount(int $buyerCount, int $itemCount): void
    {
        $today = TestDateUtil::formatToday();
        $buyerInfos = [];
        for ($i=0; $i < $buyerCount; $i++) {
            $buyerInfos[] = ShiptLogTestHelper::createBuyerInfo($itemCount, $today);
        }
        $this->assertEquals($itemCount, count($buyerInfos[0][SC::ITEMS]), '期待値側の商品数');
        $response = $this->uploadOk($buyerInfos);

       // 購入者数確認
        $response->assertJsonCount($buyerCount);
        for($i = 0; $i < $buyerCount; $i++) {
            $buyer = $buyerInfos[$i];

            // 購入者情報の確認
            $response->assertJson(function(AssertableJson $json) use($i, $buyer) {
                $json->whereAll([
                    "{$i}.". SC::PLATFORM => ShopPlatform::MERCARI->value,
                    "{$i}.". SC::PLATFORM_ORDER_ID => $buyer[SC::ORDER_ID],
                    "{$i}.". SC::BUYER => $buyer[SC::BUYER],
                    "{$i}.". SC::ZIPCODE => $buyer[SC::POSTAL_CODE],
                    "{$i}.". SC::ADDRESS =>
                        $buyer[SC::STATE].$buyer[SC::CITY].$buyer[SC::ADDRESS_1].' '.$buyer[SC::ADDRESS_2],
                    "{$i}.". SC::ITEM_COUNT => count($buyer[SC::ITEMS]),
                    ]);
                $json->missingAll([GC::ID, SC::SHIPT_DATE, SC::PREV_ID, SC::NEXT_ID])->etc();
            });
        }
    }

    #[Test]
    #[TestDox('商品が単品の場合、枚数が正しく設定されているか確認する')]
    #[TestWith([false, 1], '通常版1枚')]
    #[TestWith([false, 2], '通常版複数枚')]
    #[TestWith([true, 1], '特別版1枚')]
    #[TestWith([true, 2], '特別版複数枚')]
    public function quantitySingle(bool $isPromo, int $quantity): void {
        $buyerInfos = ShiptLogTestHelper::createBuyerInfo(1, false, $isPromo, $quantity);
        $response = $this->uploadOk([$buyerInfos]);
        $items = $buyerInfos[SC::ITEMS];
        $this->verifyItems([$items], $response);
    }

    #[Test]
    #[TestDox('商品がセット販売の場合、枚数が正しく設定されているか確認する')]
    #[TestWith([false, false, 1], 'セット販売_通常版1セット[Non-Foil]')]
    #[TestWith([true, false, 1], 'セット販売_通常版1セット[Foil]')]
    #[TestWith([false, true, 1], 'セット販売_特別版1セット[Non-Foil]]')]
    #[TestWith([true, true, 1], 'セット販売_特別版1セット[Foil]]')]
    #[TestWith([false, false, 2], 'セット販売_通常版2セット[Non-Foil]')]
    #[TestWith([true, false, 2], 'セット販売_通常版2セット[Foil]')]
    #[TestWith([false, true, 2], 'セット販売_特別版2セット[Non-Foil]]')]
    #[TestWith([true, true, 2], 'セット販売_特別版2セット[Foil]]')]
    public function quantitySetItems(bool $isFoil, bool $isPromo, int $quantity): void {
        $shipment = 2;
        $buyerInfos = ShiptLogTestHelper::createBuyerInfo(1, $isFoil, $isPromo, $shipment*$quantity);
        $buyerInfos[SC::ITEMS] = array_map(function($item) use ($shipment, $quantity) {
            $stock = Stockpile::find((int)$item[GC::ID]);
            // セット販売の商品名に変更
            $item[SC::PRODUCT_NAME] = ShiptLogTestHelper::product_name($stock, true, $shipment);
            logger()->info($item[SC::PRODUCT_NAME]);
            $item[SH::QUANTITY] = $quantity;
            return $item;
        }, $buyerInfos[SC::ITEMS]);

        $response = $this->uploadOk([$buyerInfos]);
        $items = $buyerInfos[SC::ITEMS];
        $this->verifyItems([$items], $response);
    }

    /**
     * 商品情報について検証する。
     *
     * @param array $exItems
     * @param TestResponse $response
     * @return void
     */
    private function verifyItems(array $exItems, TestResponse $response) {
        foreach ($exItems as $i => $item) {
            $base = "0.".SC::ITEMS.".{$i}.";
            $response->assertJsonPath($base.SC::SHIPMENT, $this->shipment($item[$i]));
            $response->assertJsonPath($base.SC::STOCK.'.'.GC::ID, $item[$i][GC::ID]);
        }
    }

    #[TestDox('出荷枚数が在庫枚数より少ないか同等なら出荷情報が返ってくるか確認する')]
    #[TestWith(['<'], '出荷枚数 < 在庫枚数')]
    #[TestWith(['='], '出荷枚数 = 在庫枚数')]
    public function testOkShipment(string $symbol) {
        $buyerInfo = ShiptLogTestHelper::createTodayOrderInfos();
        if ($symbol == '=') {
            $item = $buyerInfo[SC::ITEMS][0];
            $productId = $item[GC::ID];
            $stock = Stockpile::find((int)$productId);
            $item[SC::SHIPMENT] = $stock->quantity;
        }
        $this->uploadOk([$buyerInfo]);
    }

    /**
     * 出荷枚数を取得する。
     *
     * @return int
     */
    public function shipment(array $item):int {
        $product_name = $item[SC::PRODUCT_NAME];
        if (preg_match('/(\d+)枚\s*セット/u', $product_name, $matches)) {
            return  (int) $matches[1] * (int)$item[SC::QUANTITY];
        }
        return (int)$item[SC::QUANTITY];
    }

    #[Test]
    #[TestDox('送料別の場合、送料情報が正しく設定されているか確認する。')]
    #[TestWith([ShiptMethod::MINI], 'ミニレター')]
    #[TestWith([ShiptMethod::REGISTER_MAIL], '簡易書留')]
    #[TestWith([ShiptMethod::NEKOPOST], 'ネコポス')]
    #[TestWith([ShiptMethod::RAKURAKU], 'らくらくメルカリ便')]
    #[TestWith([ShiptMethod::YUPACKET], 'ゆうパケットポストmini')]
    public function testFee(ShiptMethod $method) {
        $buyerInfo = ShiptLogTestHelper::createTodayOrderInfos();
        $exFee = Shipping::findByMethod($method->value);
        if ($method == ShiptMethod::NEKOPOST || $method == ShiptMethod::RAKURAKU) {
            $buyerInfo[SC::ITEMS][0][SC::SHIPPING_METHOD] = $method->value;
        }
        $buyerInfo[SC::SHIPPING_PRICE] = $exFee->price;

        $response = $this->uploadOk([$buyerInfo]);
        $response->assertJsonPath('0.'.SC::FEE.'.'.GC::ID, $exFee->id);
        $response->assertJsonPath('0.'.SC::FEE.'.'.SC::METHOD, $exFee->name);
        $response->assertJsonPath('0.'.SC::FEE.'.'.SC::PRICE, $exFee->price);
        // 商品別の小計と単価は送料が按分されていないことを確認する。
        $response->assertJsonPath('0.'.SC::ITEMS.'.0.'.SC::SUBTOTAL, $buyerInfo[SC::ITEMS][0][SC::SUBTOTAL]);
        $response->assertJsonPath('0.'.SC::ITEMS.'.0.'.SC::UNIT_PRICE, $buyerInfo[SC::ITEMS][0][SC::PRODUCT_PRICE]);
    }

    #[TestWith([1499, ShiptMethod::MINI], '1500円未満_ミニレター')]
    #[TestWith([1500, ShiptMethod::CLICK], '1500円_クリックポスト')]
    #[TestWith([9999, ShiptMethod::CLICK], '9999円_クリックポスト')]
    #[TestWith([10000, ShiptMethod::REGISTER_MAIL], '10000円以上_簡易書留')]
    #[TestDox('送料込みの場合、価格に見合った送料が正しく設定されているか確認する')]
    public function testShippingFee(int $productPrice, ShiptMethod $method) {
        $buyerInfo = ShiptLogTestHelper::createTodayOrderInfos();
        $buyerInfo[SC::ITEMS][0][SC::PRODUCT_PRICE] = $productPrice;

        $response = $this->uploadOk([$buyerInfo]);
        $expectedFee = Shipping::findByMethod($method->value);

        $response->assertJsonPath('0.'.SC::FEE.'.'.GC::ID, $expectedFee->id);
        $response->assertJsonPath('0.'.SC::FEE.'.'.SC::METHOD, $expectedFee->name);
        $response->assertJsonPath('0.'.SC::FEE.'.'.SC::PRICE, $expectedFee->price);
    }

    #[Test]
    #[TestDox('商品の合計金額（割引前）が正しく計算されているか確認する')]
    #[TestWith([true], '送料が¥0')]
    #[TestWith([false], '送料が¥1以上')]
    public function calcSubtotal(bool $isIncluded) {
        $fee = $isIncluded ? 0 : 85;
        $buyerInfo = ShiptLogTestHelper::createBuyerInfo(2, false, false, 2);
        $buyerInfo[SC::SHIPPING_PRICE] = $fee;
        $response = $this->uploadOk([$buyerInfo]);

        $exSubtotal = $this->calcExSubtotal($buyerInfo);
        $response->assertJsonPath('0.'.SC::ITEM_SUBTOTAL, $exSubtotal);
    }

    #[TestDox('クーポン割引合計額と最終請求金額が正しく計算されているか確認する')]
    #[TestWith([0, 1], '1商品_割引なし')]
    #[TestWith([100, 1], '1商品_割引あり')]
    #[TestWith([0, 2], '2商品_割引なし')]
    #[TestWith([80, 2], '2商品_割引あり')]
    public function testTotalPriceCalc(int $discount, int $itemCount): void {
        $buyerInfo = ShiptLogTestHelper::createBuyerInfo($itemCount, false, false, 1);
        $buyerInfo[SC::DISCOUNT_AMOUNT] = $discount;
        $response = $this->uploadOk([$buyerInfo]);

        $response->assertJson(function(AssertableJson $json) use ($buyerInfo, $discount){
            $json->hasAll([
                '0.'.SC::ITEM_SUBTOTAL,
                '0.'.SC::DISCOUNT_AMOUNT,
                '0.'.SC::GRAND_TOTAL
                ])->etc();

            $exSubtotal = $this->calcExSubtotal($buyerInfo);
            $json->whereAll([
                '0.'.SC::PLATFORM_ORDER_ID => $buyerInfo[SC::ORDER_ID],
                '0.'.SC::ITEM_SUBTOTAL => $exSubtotal,
                '0.'.SC::DISCOUNT_AMOUNT => $discount,
                '0.'.SC::GRAND_TOTAL => $exSubtotal - $discount
            ])->etc();
        });
    }

    #[Test]
    #[TestDox('枚数と単価、小計について検証する。')]
    public function 商品情報() {
        $buyerInfo = ShiptLogTestHelper::createBuyerInfo(2, false, false);
        $response = $this->uploadOk([$buyerInfo]);
        $response->assertJson(function(AssertableJson $json) use ($buyerInfo) {
            $json->each(function(AssertableJson $aBuyer) use ($buyerInfo) {
                $expectedItems = $buyerInfo[SC::ITEMS];
                $aBuyer
                ->has(SC::ITEMS, 2)
                ->has(SC::ITEMS, function (AssertableJson $items) use (&$expectedItems) {
                        $exSubtotal = $this->calcExSubtotal($expectedItems);
                        $shiptFee = ShiptMethod::findByPrice($exSubtotal);
                        $feePerItem = round($shiptFee->price / 2);
                        $items->each(function (AssertableJson $item) use(&$expectedItems, &$feePerItem){
                            $ex = array_shift($expectedItems);
                            $exItemTotal = $ex[SC::PRODUCT_PRICE] - $feePerItem;
                            $item->missing(GC::ID);
                            $item->whereAllType([
                                            SC::SHIPMENT => 'integer',
                                            SC::SUBTOTAL => 'integer',
                                            SC::UNIT_PRICE => 'integer',
                                            ])
                                        ->whereAll([
                                            SC::SHIPMENT => $ex[SH::QUANTITY],
                                            SC::SUBTOTAL => (int)$exItemTotal,
                                            SC::UNIT_PRICE =>(int)round($exItemTotal / $ex[SH::QUANTITY])
                                        ])->etc();
                    });
                })->etc();
            });
        });
    }

    #[TestDox('在庫情報が正しく表示されているか確認する')]
    #[TestWith([false, false], '通常版')]
    #[TestWith([true, false], '通常版のFoilカード')]
    #[TestWith([false, true], '特別版')]
    #[TestWith([true, true], '特別版のFoilカード')]
    public function testStock(bool $isFoil = false, bool $isPromo = false): void {
        $buyerInfos = [ShiptLogTestHelper::createBuyerInfo(1, $isFoil, $isPromo)];
        $response = $this->uploadOk($buyerInfos);

        $items = $buyerInfos[0][SC::ITEMS];
        foreach ($items as $i => $item) {
            $stock = Stockpile::find((int)$item[GC::ID]);
            $card = $stock->cardinfo;
            $exp = $card->expansion;
            $foil = $card->foiltype;
            $promo = $card->promotype;

            $response->assertJson(function(AssertableJson $json) use($i, $stock, $card, $exp, $foil, $promo) {
                // 共通ベースパス
                $base = "0." . SC::ITEMS . ".{$i}." . SC::STOCK;
                $expected = [
                    $base.".".GC::ID => $stock->id,
                    $base.".".CC::CARD.".".GC::NAME => $card->name,
                    $base.".".CC::CARD.".".CC::NUMBER => $card->number,
                    $base.".".CC::CARD.".".CC::COLOR => $card->color_id,
                    $base.".".CC::CARD.".".CC::IMAGE_URL => $card->image_url,
                    $base.".".CC::CARD.".".CC::EXP.".".GC::NAME => $exp->name,
                    $base.".".CC::CARD.".".CC::EXP.".".CC::ATTR => $exp->attr,
                    $base.".".CC::CARD.".".CC::FOIL.".".GC::ID => $foil->id,
                    $base.".".CC::CARD.".".CC::FOIL.".".GC::NAME => $foil->name,
                    $base.".".CC::CARD.".".CC::PROMOTYPE.".".GC::ID => $promo->id,
                    $base.".".CC::CARD.".".CC::PROMOTYPE.".".GC::NAME => $promo->name,
                    $base.".".SH::LANG => $stock->language,
                    $base.".".SH::CONDITION => $stock->condition,
                    $base.".".SH::QUANTITY => $stock->quantity,
                ];

                $json->whereAll($expected);
            });
        }
    }

    #[TestDox('isRegisteredフラグが正しく設定されているか確認する')]
    #[TestWith([false], '未登録')]
    #[TestWith([true], '登録済み')]
    public function testIsRegistered(bool $isRegistered) {
        $buyerInfos = [ShiptLogTestHelper::createTodayOrderInfos()];
        if ($isRegistered) {
            $this->seed(TestOrderSeeder::class);
            $order = Orders::inRandomOrder()->first();
            $buyerInfos[0][SC::ORDER_ID] = $order->platform_order_id;
            $buyerInfos[0][SC::BUYER] = $order->buyer_name;

            $orderitem = $order->orderitems->first();
            $buyerInfos[0][SC::ITEMS][0][GC::ID] = $orderitem->stock_id;
            $buyerInfos[0][SC::ITEMS][0][ SH::QUANTITY] = $orderitem->quantity;
        }

        $response = $this->uploadOk($buyerInfos);
        $response->assertJsonPath('0.'.SC::ITEMS.'.0.'.SC::IS_REGISTERED, $isRegistered);
    }

    /**
     * アップロードOKパターン
     *
     * @param array $buyerInfos
     * @return TestResponse $response
     */
    private function uploadOk(array $buyerInfos) {
        $orderIds = array_map(function($info) {return $info[SC::ORDER_ID];}, $buyerInfos);
        $this->setMockCardBoard($orderIds);

        $implode = $this->createCsvLine($buyerInfos);
        $header = ShiptLogTestHelper::getHeader();
        $content = <<<CSV
        {$header}
        {$implode}
        CSV;
        $response = $this->upload($content);
        $response->assertJsonStructure([
            '*' => [
                SC::PLATFORM,
                SC::PLATFORM_ORDER_ID,
                SC::BUYER,
                SC::ZIPCODE,
                SC::ADDRESS,
                SC::ITEM_SUBTOTAL,
                SC::ITEM_COUNT,
                SC::DISCOUNT_AMOUNT,
                SC::FEE,
                SC::ITEMS => [
                    '*' => [
                        SC::STOCK => [
                            GC::ID,
                            CC::CARD => [
                                GC::NAME,
                                CC::EXP => [
                                    GC::NAME,
                                    CC::ATTR
                                ],
                                CC::NUMBER,
                                CC::IMAGE_URL,
                                CC::COLOR,
                                CC::FOIL => [
                                    GC::ID,
                                    GC::NAME
                                ],
                                CC::PROMOTYPE => [
                                    GC::ID,
                                    GC::NAME
                                ]
                            ],
                            SH::CONDITION,
                            SH::LANG,
                            SH::QUANTITY
                        ],
                        SC::STOCK,
                        SC::SHIPMENT,
                        SC::SUBTOTAL,
                        SC::UNIT_PRICE,
                        SC::IS_REGISTERED
                        ]
                    ]
                ]
            ]);

            $response->assertJsonMissingPath('*.'.'shipping_date');

            return $response;
    }

    /**
     * CardBoardServiceのMockを設定する。
     *
     * @param array $orderIds
     * @return void
     */
    private function setMockCardBoard(array $orderIds) {
        $mock = \Mockery::mock(CardBoardService::class);
        $errorId = 'error';
        foreach($orderIds as $id) {
            if ($id === $errorId) {
                continue;
            }
            $mock->shouldReceive('findByOrderId')
                    ->with($id)
                    ->andReturn(collect([
                        (object)[
                            'id' => 123,
                            'title' => 'テストカード'
                        ]
             ]));
        }
        $mock->shouldReceive('findByOrderId')
        ->with($errorId)
        ->andReturn(collect([]));

        $this->app->instance(\App\Services\CardBoardService::class, $mock);
    }

    #[TestDox('ファイルエラー: ヘッダー不足')]
    #[Group('file-error')]
    public function testNgLackOfHeader(): void {
        $buyerInfo = ShiptLogTestHelper::createTodayOrderInfos();
        $header  = ShiptLogTestHelper::getHeader();
        // shipping_dateヘッダーを削除
        $header = str_replace(SC::PRODUCT_ID, '', $header);
        $implode = $this->createCsvLine([$buyerInfo]);
        $content = <<<CSV
        {$header}
        {$implode}
        CSV;
        $this->verifyFileError($content, 'lack-of-header', SC::PRODUCT_ID);
    }

    #[TestDox('ファイルエラー: ヘッダーがない')]
    #[Group('file-error')]
    public function testNgNoHeader() : void {
        $buyerInfo = ShiptLogTestHelper::createTodayOrderInfos();
        $implode = $this->createCsvLine([$buyerInfo]);
        $content = <<<CSV
        {$implode}
        CSV;
        $this->verifyFileError($content, 'no-header');
    }

    #[TestDox('ファイルエラー: データ行がない')]
    #[Group('file-error')]
    public function testNgEmptyData(): void {
        $header  = ShiptLogTestHelper::getHeader();
        $content = <<<CSV
        {$header}
        CSV;
        $this->verifyFileError($content, 'empty-content');
    }

    #[TestDox('ファイルエラー: 空ファイル')]
    #[Group('file-error')]
    public function testNgEmptyFile(): void {
        $expJson = [
            EC::TITLE => 'Validation Error',
            EC::DETAIL => 'ファイルが空です',
        ];
        $this->uploadNg('', Response::HTTP_BAD_REQUEST, $expJson);
    }

    #[TestDox('ファイルエラー: CSV形式以外のファイルをアップロード')]
    #[Group('file-error')]
    public function testNgPngFile(): void {
        $image = UploadedFile::fake()->create('test_image.png', 100, 'image/png');
        $response = $this->uploadFile($image, Response::HTTP_BAD_REQUEST);
        $this->assertJsonError($response, Response::HTTP_BAD_REQUEST, [
            EC::TITLE => 'Validation Error',
            EC::DETAIL => 'ファイルはCSV形式でアップロードしてください']);
    }

    #[TestDox('不正な商品情報がある場合、行数とメッセージが返ってくるか検証する。')]
    #[TestWith([SC::ORDER_ID, 'error', 'no-notion', 2], '注文番号が入力されたNotionカードが存在しない')]
    #[TestWith([SC::QUANTITY, '999', 'excess-shipment'], '出荷枚数が在庫枚数より多い')]
    #[TestWith([GC::ID, '3', 'zero_quantity'], '在庫枚数が無い')]
    public function testNgItemError(string $key, string $value, string $msg, int $row = 3): void {
        $buyerInfo = ShiptLogTestHelper::createTodayOrderInfos();
        if (key_exists($key, $buyerInfo)) {
            $buyerInfo[$key] =$value;
        } else {
            $buyerInfo[SC::ITEMS][0][$key] = $value;
        }

        $implode = $this->createCsvLine([$buyerInfo]);
        $header = ShiptLogTestHelper::getHeader();
        $content = <<<CSV
        {$header}
        {$implode}
        CSV;

        $base = 'validation.items';
        $this->setMockCardBoard([$buyerInfo[SC::ORDER_ID]]);
        $status = CustomResponse::HTTP_CSV_VALIDATION;

        $response = $this->upload($content, $status);
        $this->assertRowError($response,  __("$base.$msg.detail"), $row);
    }

    #[TestDox('バリデーションエラーが発生した時のエラー情報を検証する。')]
    public function testNgValidator(): void
    {
        $buyerInfo = ShiptLogTestHelper::createTodayOrderInfos();
        $buyerInfo[SC::POSTAL_CODE] = 'aaa';

        $implode = ShiptLogTestHelper::createCsvLine([$buyerInfo]);
        $header = ShiptLogTestHelper::getHeader();
        $content = <<<CSV
        {$header}
        {$implode}
        CSV;

        $this->setMockCardBoard([$buyerInfo[SC::ORDER_ID]]);
        $status = CustomResponse::HTTP_CSV_VALIDATION;
        $response = $this->upload($content, $status);
        $this->assertRowError($response, '郵便番号は「123-4567」の形式で入力してください。');
    }

    /**
     * 商品の合計金額（割引前）を期待値から計算する。
     *
     * @param array $buyerInfo
     * @return integer
     */
    private function calcExSubtotal(array $buyerInfo):int
    {
        return $buyerInfo[SC::ORDER_PRICE] + $buyerInfo[SC::SHIPPING_PRICE];
    }

    private function verifyFileError(string $content, string $keyword, string $value = ''): void {
        $base = 'validation.file';
        $expJson = [
            EC::TITLE => __("$base.title.$keyword"),
            EC::DETAIL => __("$base.detail.$keyword", ['values' => $value])
        ];
        $this->uploadNg($content, CustomResponse::HTTP_CSV_VALIDATION, $expJson);
    }

    private function uploadNg(string $content, int $status, array $expJson = []): TestResponse {
        $response = $this->upload($content, $status);
        $this->assertJsonError($response, $status, $expJson);
        return $response;
    }

    /**
     * エラーに関するJSON情報を検証する。
     *
     * @param TestResponse $response
     * @param integer $status
     * @param array $expJson
     * @return void
     */
    private function assertJsonError(TestResponse $response, int $status, array $expJson = []): void {
        $response->assertJson(function(AssertableJson $json) use($status, $expJson) {
            $json->hasAll([EC::TITLE, GC::STATUS, EC::REQUEST, EC::DETAIL]);
            $json->whereAll([
                EC::TITLE => $expJson[EC::TITLE],
                GC::STATUS => $status,
                EC::DETAIL => $expJson[EC::DETAIL],
                EC::REQUEST => 'api/shipping/parse',
            ]);
        });
    }

    /**
     * CSVファイルの内容で発生したエラー情報を検証する。
     *
     * @param TestResponse $response
     * @param string $msg
     * @return void
     */
    public function assertRowError(TestResponse $response,  string $msg, int $row = 2) {
        $this->assertCsvRowError($response, 'api/shipping/parse', [EC::ROW=> $row, EC::MSG => $msg]);
    }

    /**
     * CSV1行分の文字列を作成する。
     *
     * @param array $buyers
     * @return string
     */
    private function createCsvLine(array $buyers):string {
        $implode = '';
        foreach($buyers as $buyer) {
            $oneline = [
                $buyer[SC::TYPE], $buyer[SC::ORDER_ID], $buyer[SC::BUYER]];
            $oneline = ShiptLogTestHelper::padBlankToArray($oneline, 3);
            array_push($oneline, $buyer[SC::ORDER_PRICE]);
            $oneline = ShiptLogTestHelper::padBlankToArray($oneline, 1);
            array_push($oneline, $buyer[SC::SHIPPING_PRICE], $buyer[SC::DISCOUNT_AMOUNT]);
            $oneline = ShiptLogTestHelper::padBlankToArray($oneline, 6);

            $items = $buyer[SC::ITEMS];
            $implode .= ShiptLogTestHelper::arrayToCsvString($oneline)."\n";
            foreach($items as $item) {
                $itemLine = [$item[SC::TYPE], $buyer[SC::ORDER_ID], ""];
                array_push($itemLine, $item[GC::ID], $item[SC::PRODUCT_NAME],
                         $item[StockpileHeader::QUANTITY], "", $item[SC::PRODUCT_PRICE]);
                $itemLine = ShiptLogTestHelper::padBlankToArray($itemLine, 1);
                array_push($itemLine, $item[SC::SHIPPING_METHOD], $buyer[SC::POSTAL_CODE], $buyer[SC::STATE], $buyer[SC::CITY],
                         $buyer[SC::ADDRESS_1], $buyer[SC::ADDRESS_2]);
                $implode .= ShiptLogTestHelper::arrayToCsvString($itemLine)."\n";
            }
        }
        return $implode;
    }

    /**
     * Undocumented function
     *
     * @param string $content
     * @param int $status
     * @return \Illuminate\Testing\TestResponse
     */
    private function upload(string $content = '', int $status = Response::HTTP_CREATED) {
        Storage::fake('local');
        $tmpFilePath = tempnam(sys_get_temp_dir(), 'shipping_import_');
        $filename = basename($tmpFilePath).'.csv';
        try {
            // ダミーCSVファイル作成
            file_put_contents($tmpFilePath, $content);

            // 一時ファイルから UploadedFile インスタンス作成
            $file = new UploadedFile(
                $tmpFilePath, $filename, 'text/csv', null, true);
            $response = $this->uploadFile($file, $status);
            return $response;
        } finally {
            if (file_exists($tmpFilePath)) {
                unlink($tmpFilePath);
            }
        }
    }

    /**
     * UploadedFileオブジェクトを指定してアップロードテストを実行する。
     *
     * @param UploadedFile $file
     * @param int $status
     * @return \Illuminate\Testing\TestResponse
     */
    private function uploadFile(UploadedFile $file, int $status = Response::HTTP_CREATED) {
        $response = $this->postJson('/api/shipping/parse', ['file' => $file], [
            'Content-Type' => 'multipart/form-data',
        ]);
        if ($response->getStatusCode() != $status) {
            logger()->error($response->json(EC::DETAIL));
        }
        $response->assertStatus($status);
        return $response;
    }
}
