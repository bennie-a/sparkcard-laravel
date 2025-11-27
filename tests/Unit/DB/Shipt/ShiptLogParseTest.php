<?php

namespace Tests\Feature\tests\Unit\DB\Shipt;

use App\Models\Stockpile;
use App\Services\Constant\CardConstant;
use App\Services\Constant\GlobalConstant as GC;
use App\Services\Constant\ShiptConstant as SC;
use App\Services\Constant\StockpileHeader;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\TestWith;
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

    #[TestDox('購入者と注文商品が正しく集計されているか確認する')]
    #[TestWith([1, 1], '購入者1人_出荷商品1件')]
    #[TestWith([1, 2], '購入者1人_出荷商品2件')]
    #[TestWith([2, 1], '購入者2人_出荷商品1件')]
    #[TestWith([2, 1], '購入者2人_出荷商品2件')]
    public function testBuyerAndItemCount(int $buyerCount, int $itemCount): void
    {
        $today = TestDateUtil::formatToday();
        $response = $this->uploadOk($buyerCount, $itemCount, $today);            
        // 購入者数確認
        $response->assertJsonCount($buyerCount);

        //注文商品の件数確認
        for($i = 0; $i < $buyerCount; $i++) {
            $response->assertJsonPath("{$i}.". SC::ITEMS, function($items) use($itemCount){
                return count($items) === $itemCount;
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
        $response = $this->uploadOk(1, 1, $date);
        if (empty($date)) {
            $date = TestDateUtil::formatToday();
        }
        $response->assertJsonPath('0.'.SC::SHIPPING_DATE, $date);
    }

    /**
     * アップロードOKパターン
     *
     * @param integer $buyerCount
     * @param integer $itemCount
     * @param string $shiptDate
     * @return \Illuminate\Testing\TestResponse
     */
    private function uploadOk(int $buyerCount, int $itemCount, string $shiptDate) {
        $buyerInfos = [];
        for ($i=0; $i < $buyerCount; $i++) { 
            $buyerInfos[] = $this->createBuyerInfo($itemCount, $shiptDate);
        }
        
        $implode = $this->createCsvLine($buyerInfos);
        $content = <<<CSV
        {$this->getHeader()}
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
                            CardConstant::CARD => [
                                GC::NAME,
                                CardConstant::EXP => [
                                    GC::NAME,
                                    CardConstant::ATTR
                                ],
                                CardConstant::NUMBER,
                                CardConstant::IMAGE_URL,
                                CardConstant::COLOR,
                                CardConstant::FOIL => [
                                    GC::ID,
                                    GC::NAME
                                ],
                                CardConstant::PROMOTYPE => [
                                    GC::ID,
                                    GC::NAME
                                ]
                            ],
                            StockpileHeader::CONDITION,
                            StockpileHeader::LANG,
                            StockpileHeader::QUANTITY
                        ],
                        SC::SHIPMENT,
                        SC::SINGLE_PRICE,
                        SC::SUBTOTAL_PRICE
                        ]
                    ]
                ]
            ]);

        return $response;
    }    
    
    private function createCsvLine(array $buyers):string {
        $implode = '';
        foreach($buyers as $buyer) {
            $items = $buyer[SC::ITEMS];
            unset($buyer[SC::ITEMS]);
            $buyerLine = array_values($buyer); 
            foreach($items as $item) {
                $oneline = $buyerLine;
                $oneline = array_merge($buyerLine, array_values($item));
                $implode .= $this->arrayToCsvString($oneline)."\n";
                $oneline = [];
            }
        }
        return $implode;
    }

    /**
     * 購入者情報をランダムで作成する。
     *
     * @return array
     */
    private function createBuyerInfo(int $itemCount, string $shiptDate, 
                                                            bool $isFoil = false, bool $isPromo = false):array {
        $items = [];
        for ($i=0; $i < $itemCount; $i++) { 
            $items[] = $this->createItemInfo($isFoil, $isPromo);
        }
        return [
            SC::ORDER_ID => $this->createOrderId(),
            SC::BUYER => fake()->name(),
            SC::POSTAL_CODE => fake()->postcode1()."-".fake()->postcode2(),
            SC::STATE => fake()->prefecture(),
            SC::CITY => fake()->city(),
            SC::ADDRESS_1 => fake()->streetAddress(),
            SC::ADDRESS_2 => fake()->secondaryAddress(),
            SC::SHIPPING_DATE => $shiptDate,
            SC::ITEMS => $items
        ];
    }

    /**
     * 商品情報を1件作成する。
     *
     * @param integer $foiltypeId
     * @param integer $promotypeId
     * @return array
     */
    private function createItemInfo(bool $isFoil, bool $isPromo):array {
        $isFoilOpe = !$isFoil ? '=' : '<>';
        $isPromoOpe = !$isPromo ? '=' : '<>';
        $stock = Stockpile::inRandomOrder()->
                            where(StockpileHeader::QUANTITY, '>', 0)
                            ->whereHas('cardinfo', function($query) use($isFoilOpe, $isPromoOpe){
                                $query->where(CardConstant::FOIL_ID, $isFoilOpe, 1)
                                ->where(CardConstant::PROMO_ID, $isPromoOpe, 1);
                            })->first();
        return [GC::ID => $stock->id, SC::PRODUCT_NAME => $this->product_name($stock),
                    StockpileHeader::QUANTITY => fake()->numberBetween(1, $stock->quantity),
                    SC::PRODUCT_PRICE => fake()->numberBetween(300, 10000), SC::DISCOUNT_AMOUNT => 0];
    }

    /**
     * 在庫情報から商品名を作成する。
     *
     * @param Stockpile $stock
     * @return string
     */
    private function product_name(Stockpile $stock): string {
        $card = $stock->cardinfo;
        $exp = $stock->cardinfo->expansion;
        $foil = $stock->cardinfo->foiltype;
        $promo = $stock->cardinfo->promotype;
        return "【{$exp->attr}】".
                ($foil->id != 1 ? "【{$foil->name}】" : "").
                "{$card->name}".
                ($promo->id != 1 ? "≪{$promo->name}≫" : "").
                "[$stock->language]"."[{$card->color_id}]";
    }

//     public function test_ng_ヘッダー不足(): void {
//         $content = <<<'CSV'
//         order_id,buyer_name,original_product_id,product_name,quantity,product_price,shipping_postal_code,shipping_state,shipping_city,shipping_address_1,shipping_address_2,coupon_discount_amount
//         order_2JGEXf3shdRmUSLVLKiR3U,梶島 充雄,1111,【BRO】ガイアの眼、グウェナ[JP][緑],1,340,270-1164,千葉県,我孫子市,つくし野3-20,我孫子ビレジ504,0
//         order_2JGEXf3shdRmUSLVLKiR3U,梶島 充雄,1112,【SPM】インポスター症候群[JP][青],1,480,270-1164,千葉県,我孫子市,つくし野3-20,我孫子ビレジ504,00
// CSV;
//         $response = $this->upload($content, CustomResponse::HTTP_CSV_VALIDATION);
//         $response->assertJson(function(AssertableJson $json) {
//             $json->hasAll(['title', 'status', 'request', 'detail']);
//             $json->whereAll([
//                 'title' => 'ヘッダー不足',
//                 'status' => CustomResponse::HTTP_CSV_VALIDATION,
//                 'detail' => 'ヘッダーが足りません: shipping_date',
//                 'request' => 'api/shipping/parse',
//             ]);
//         });
//     }

    /**
     * Undocumented function
     *
     * @param string $content
     * @param int $status
     * @return \Illuminate\Testing\TestResponse
     */
    private function upload(string $content = '', int $status = Response::HTTP_CREATED) {
        Storage::fake('local');
        // ダミーCSVファイル作成
        $filename = 'shipping_import_test.csv';
        $tmpFilePath = sys_get_temp_dir() . "/{$filename}";
        file_put_contents($tmpFilePath, $content);

        try {
            // 一時ファイルから UploadedFile インスタンス作成
            $file = new UploadedFile(
                $tmpFilePath, $filename, 'text/csv', null, true);
    
            $response = $this->postJson('/api/shipping/parse', ['file' => $file], [
                'Content-Type' => 'multipart/form-data',
            ]);
            
            $response->assertStatus($status);
            return $response;
        } finally {
            if (file_exists($tmpFilePath)) {
                unlink($tmpFilePath);
            }
        }
    }

    /**
     * 注文番号をランダムで作成する。
     *
     * @return string
     */
    private function createOrderId(): string {
        return uniqid('order_');
    }

    /**
     * 配列を','区切りの文字列に変換する。
     *
     * @param [type] $array
     * @return string
     */
    private function arrayToCsvString($array):string
    {
        return implode(',', $array);
    }

    /**
     * 注文CSV用ヘッダー行を取得する。
     *
     * @return array
     */
    private function getHeader() {
        $header = [
                                SC::ORDER_ID, SC::BUYER, SC::POSTAL_CODE, SC::STATE,
                                SC::CITY, SC::ADDRESS_1, SC::ADDRESS_2, SC::SHIPPING_DATE,
                                SC::PRODUCT_ID, SC::PRODUCT_NAME, StockpileHeader::QUANTITY,
                                SC::PRODUCT_PRICE, SC::DISCOUNT_AMOUNT
                            ];
        return $this->arrayToCsvString($header);
    }
}
