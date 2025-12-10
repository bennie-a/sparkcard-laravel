<?php

namespace Tests\Unit\DB\Shipt;
use App\Enum\CsvFlowType;
use App\Enum\ShopPlatform;
use App\Http\Response\CustomResponse;
use App\Models\Stockpile;
use App\Services\CardBoardService;
use App\Services\Constant\CardConstant as CC;
use App\Services\Constant\GlobalConstant as GC;
use App\Services\Constant\ShiptConstant as SC;
use App\Services\Constant\ErrorConstant as EC;
use App\Services\Constant\StockpileHeader;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Testing\TestResponse;
use Mockery;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\Unit\DB\Shipt\ShiptLogTestHelper;
use Tests\Database\Seeders\DatabaseSeeder;
use Tests\Database\Seeders\TestCardInfoSeeder;
use Tests\Database\Seeders\TestStockpileSeeder;
use Tests\Database\Seeders\TruncateAllTables;
use Tests\TestCase;
use Tests\Util\TestDateUtil;

/**
 * 出荷情報解析機能のテストケース
 */
class ShiptLogParseTest extends TestCase
{
    public function setup():void {
        parent::setup();
        $this->seed(TruncateAllTables::class);
        $this->seed(DatabaseSeeder::class);
        $this->seed(TestCardInfoSeeder::class);
        $this->seed(TestStockpileSeeder::class);
     }

    #[TestDox('購入者情報と注文商品が正しく集計されているか確認する')]
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

        $response = $this->uploadOk($buyerInfos);

       // 購入者数確認
        $response->assertJsonCount($buyerCount);

        for($i = 0; $i < $buyerCount; $i++) {
            $buyer = $buyerInfos[$i];
            // 購入者情報の確認
            $response->assertJson(function(AssertableJson $json) use($i, $buyer) {
                $json->whereAll([
                    "{$i}.". SC::ORDER_ID => $buyer[SC::ORDER_ID],
                    "{$i}.". SC::BUYER => $buyer[SC::BUYER],
                    "{$i}.". SC::ZIPCODE => $buyer[SC::POSTAL_CODE],
                    "{$i}.". SC::ADDRESS =>
                        $buyer[SC::STATE].$buyer[SC::CITY].$buyer[SC::ADDRESS_1].' '.$buyer[SC::ADDRESS_2],
                    "{$i}.". SC::SHIPPING_DATE => $buyer[SC::SHIPPING_DATE],
                    "{$i}.". SC::ITEMS => fn($items) => count($items) == count($buyer[SC::ITEMS]),
                ]);
            });
        }
    }

    #[TestDox('発送日が正しく設定されているか確認する')]
    #[TestWith(['today'], '今日')]
    #[TestWith(['tomorrow'], '明日')]
    #[TestWith(['yesterday'], '昨日')]
    #[TestWith([''], '未入力')]
    public function testShippingDate(string $date) {
        $date = match($date) {
            'today' => TestDateUtil::formatToday(),
            'tomorrow' => TestDateUtil::formatTomorrow(),
            'yesterday' => TestDateUtil::formatYesterday(),
            '' => ''
        };
        logger()->info("Testing shipping date: {$date}");
        $buyerInfos = [ShiptLogTestHelper::createBuyerInfo(1, $date)];

        $response = $this->uploadOk($buyerInfos);

        if (empty($date)) {
            $date = TestDateUtil::formatToday();
        }
        $response->assertJsonPath('0.'.SC::SHIPPING_DATE, $date);
    }

    #[TestDox('出荷枚数が正しく設定されているか確認する')]
    #[TestWith([false, false, false], '単品_通常版[Non-Foil]')]
    #[TestWith([true, false, false], '単品_通常版[Foil]')]
    #[TestWith([false, true, false], '単品_特別版[Non-Foil]')]
    #[TestWith([true, true, false], '単品_特別版[Foil]')]
    #[TestWith([false, false, true], 'セット販売_通常版[Non-Foil]')]
    #[TestWith([true, false, true], 'セット販売_通常版[Foil]')]
    #[TestWith([false, true, true], 'セット販売_特別版[Non-Foil]]')]
    #[TestWith([true, true, true], 'セット販売_特別版[Foil]]')]
    public function testShipment(bool $isFoil, bool $isPromo, bool $isSet): void {
        $shipment = $isSet ? 2 : 1;
        $buyerInfos = [ShiptLogTestHelper::createBuyerInfo(1, TestDateUtil::formatToday(), $isFoil, $isPromo, $shipment)];
        $buyerInfos[0][SC::ITEMS] = array_map(function($item) use ($isSet, $shipment) {
            $stock = Stockpile::find((int)$item[GC::ID]);
            // セット販売の商品名に変更
            $item[SC::PRODUCT_NAME] = ShiptLogTestHelper::product_name($stock, $isSet);
            logger()->info($item[SC::PRODUCT_NAME]);
            $item[StockpileHeader::QUANTITY] = $isSet ? 1:$shipment;
            return $item;
        }, $buyerInfos[0][SC::ITEMS]);

        $response = $this->uploadOk($buyerInfos);
        $items = $buyerInfos[0][SC::ITEMS];
        foreach ($items as $i => $item) {
                $response->assertJson(function(AssertableJson $json) use($i, $item) {
                    $base = "0.".SC::ITEMS.".{$i}.";
                    $json->whereAll([
                        $base.SC::SHIPMENT => $this->shipment($item),
                        $base.SC::PRODUCT_PRICE => $item[SC::PRODUCT_PRICE],
                    ]);
            });
        }
    }

    /**
     * 出荷枚数を取得する。
     *
     * @return int
     */
    public function shipment(array $item):int {
        $product_name = $item[SC::PRODUCT_NAME];
        if (preg_match('/(\d+)枚\s*セット/u', $product_name, $matches)) {
            return  (int) $matches[1];
        }
        return (int)$item[SC::QUANTITY];
    }

    #[TestDox('支払い金額が正しく計算されているか確認する')]
    #[TestWith([0], '割引なし')]
    #[TestWith([100], '割引あり')]
    public function testTotalPriceCalc(int $discount) {
        $buyerInfos = [$this->createTodayOrderInfos(1)];
        // 商品価格と割引金額を設定
        $buyerInfos[0][SC::ITEMS][0][SC::DISCOUNT_AMOUNT] = $discount;

        $response = $this->uploadOk($buyerInfos);

        $exitem = current($buyerInfos[0][SC::ITEMS]);
        $exProductPrice = $exitem[SC::PRODUCT_PRICE];
        $exTotalPrice = $exProductPrice - $exitem[SC::DISCOUNT_AMOUNT];
        $response->assertJsonPath('0.'.SC::ITEMS.'.0.'.SC::PRODUCT_PRICE, $exProductPrice);
        $response->assertJsonPath('0.'.SC::ITEMS.'.0.'.SC::TOTAL_PRICE, $exTotalPrice);
    }

    #[TestDox('単価が正しく計算されているか確認する')]
    #[TestWith([1], '出荷枚数1枚')]
    #[TestWith([3], '出荷枚数が複数枚_割引なし')]
    #[TestWith([3, 50], '出荷枚数が複数枚_割引あり')]
    public function testSinglePriceCalc(int $shipment, int $discount = 0): void {
        $buyerInfos = [$this->createTodayOrderInfos(1)];
        // 商品価格と出荷枚数を設定
        $buyerInfos[0][SC::ITEMS][0][SC::PRODUCT_PRICE] = 1000;
        $buyerInfos[0][SC::ITEMS][0][StockpileHeader::QUANTITY] = $shipment;
        $buyerInfos[0][SC::ITEMS][0][SC::DISCOUNT_AMOUNT] = $discount;
        $response = $this->uploadOk($buyerInfos);

        $items = $buyerInfos[0][SC::ITEMS];
        for ($i=0; $i < count($items); $i++) {
            $item = $items[$i];
            $exTotalPrice = $item[SC::PRODUCT_PRICE] - $item[SC::DISCOUNT_AMOUNT];
            $exSinglePrice = (int)round($exTotalPrice / $item[StockpileHeader::QUANTITY]);
            $response->assertJson(function(AssertableJson $json) use($i, $exSinglePrice) {
            $json->whereAll([
                "0.".SC::ITEMS.".{$i}.".SC::SINGLE_PRICE => $exSinglePrice,
                ]);
            });
        }
    }

    #[TestDox('在庫情報が正しく表示されているか確認する')]
    #[TestWith([false, false], '通常版')]
    #[TestWith([true, false], '通常版のFoilカード')]
    #[TestWith([false, true], '特別版')]
    #[TestWith([true, true], '特別版のFoilカード')]
    public function testStock(bool $isFoil = false, bool $isPromo = false): void {
        $buyerInfos = [ShiptLogTestHelper::createBuyerInfo(1, TestDateUtil::formatToday(), $isFoil, $isPromo)];
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
                    $base.".".StockpileHeader::LANG => $stock->language,
                    $base.".".StockpileHeader::CONDITION => $stock->condition,
                    $base.".".StockpileHeader::QUANTITY => $stock->quantity,
                ];

                $json->whereAll($expected);
            });
        }
    }

    /**
     * アップロードOKパターン
     *
     * @param array $buyerInfos
     * @return TestResponse $response
     */
    private function uploadOk(array $buyerInfos) {
        $mock = \Mockery::mock(CardBoardService::class);
        $mock->shouldReceive('findByOrderId')
                ->with(Mockery::any())
                ->andReturn(collect([
                    (object)[
                        'id' => 123,
                        'title' => 'テストカード'
                    ]
         ]));

        $this->app->instance(\App\Services\CardBoardService::class, $mock);

        $implode = $this->createCsvLine($buyerInfos);
        $header = ShiptLogTestHelper::getHeader();
        $content = <<<CSV
        {$header}
        {$implode}
        CSV;
        $response = $this->upload($content);
                $response->assertJsonStructure([
            '*' => [
                SC::ORDER_ID,
                SC::BUYER,
                SC::SHIPPING_DATE,
                SC::ZIPCODE,
                SC::ADDRESS,
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
                            StockpileHeader::CONDITION,
                            StockpileHeader::LANG,
                            StockpileHeader::QUANTITY
                        ],
                        SC::SHIPMENT,
                        SC::PRODUCT_PRICE,
                        SC::DISCOUNT_AMOUNT,
                        SC::TOTAL_PRICE,
                        SC::SINGLE_PRICE,
                        ]
                    ]
                ]
            ]);

        return $response;
    }

    #[Group('file-error')]
    public function test_ng_ヘッダー不足(): void {
        $buyerInfo = $this->createTodayOrderInfos();
        $header  = ShiptLogTestHelper::getHeader();
        // shipping_dateヘッダーを削除
        $header = str_replace(SC::SHIPPING_DATE, '', $header);
        $implode = $this->createCsvLine([$buyerInfo]);
        $content = <<<CSV
        {$header}
        {$implode}
        CSV;
        $this->verifyFileError($content, 'lack-of-header', SC::SHIPPING_DATE);
    }

    #[Group('file-error')]
    public function test_ng_ヘッダーなし() : void {
        $buyerInfo = $this->createTodayOrderInfos();
        $implode = $this->createCsvLine([$buyerInfo]);
        $content = <<<CSV
        {$implode}
        CSV;
        $this->verifyFileError($content, 'no-header');
    }

    #[Group('file-error')]
    public function test_ng_データが空(): void {
        $header  = ShiptLogTestHelper::getHeader();
        $content = <<<CSV
        {$header}
        CSV;
        $this->verifyFileError($content, 'empty-content');
    }

    #[Group('file-error')]
    public function test_ng_空ファイル(): void {
        $expJson = [
            EC::TITLE => 'Validation Error',
            EC::DETAIL => 'ファイルが空です',
        ];
        $this->uploadNg('', Response::HTTP_BAD_REQUEST, $expJson);
    }

    #[Group('file-error')]
    public function test_ng_CSV形式以外のファイル(): void {
        $image = UploadedFile::fake()->create('test_image.png', 100, 'image/png');
        $response = $this->uploadFile($image, Response::HTTP_BAD_REQUEST);
        $this->assertJsonError($response, Response::HTTP_BAD_REQUEST, [
            EC::TITLE => 'Validation Error',
            EC::DETAIL => 'ファイルはCSV形式でアップロードしてください']);
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
     * CSV1行分の文字列を作成する。
     *
     * @param array $buyers
     * @return string
     */
    private function createCsvLine(array $buyers):string {
        $implode = '';
        foreach($buyers as $buyer) {
            $items = $buyer[SC::ITEMS];
            unset($buyer[SC::ITEMS]);
            $buyerLine = array_values($buyer);
            foreach($items as $item) {
                $oneline = $buyerLine;
                $oneline = array_merge($buyerLine, array_values($item));
                $implode .= ShiptLogTestHelper::arrayToCsvString($oneline)."\n";
                $oneline = [];
            }
        }
        return $implode;
    }

    /**
     * 発送日が今日の注文情報を取得する。
     *
     * @param integer $itemCount
     * @return array
     */
    private function createTodayOrderInfos(): array {
        return ShiptLogTestHelper::createBuyerInfo(1, TestDateUtil::formatToday());
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
        $response->assertStatus($status);
        return $response;
    }
}
