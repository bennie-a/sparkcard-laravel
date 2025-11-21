<?php

namespace Tests\Feature\tests\Unit\DB\Shipt;

use App\Models\Stockpile;
use App\Services\Constant\CardConstant;
use App\Services\Constant\GlobalConstant as GC;
use App\Services\Constant\ShiptConstant as SC;
use App\Services\Constant\StockpileHeader;
use Database\Seeders\CsvHeaderSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    public function test_注文数が1件(): void
    {
        $buyerInfo = $this->createBuyerInfo();
        $today = TestDateUtil::formatToday();
        
        $implode = '';
        $csvline = array_values($buyerInfo);            
        for($i = 0; $i < 2; $i++) {
            $oneline = $csvline;
            array_push($oneline, $today);
            $stock = $this->createItemInfo();
            $buyerInfo[SC::ITEMS][] = $stock;
            $oneline += $stock;
            $implode .= $this->arrayToCsvString($oneline)."\n";
            $oneline = [];
        }

        $content = <<<CSV
        {$this->getHeader()}
        {$implode}
        CSV;
        $response = $this->upload($content);
        $json = $response->json();
        logger()->debug($json);
    }
    
    public function test_購入者が2名_1人あたりの注文数が3件ずつ(): void {
        $today = TestDateUtil::formatToday();
        $content = <<<CSV
        order_id,buyer_name,shipping_date,original_product_id,product_name,quantity,product_price,shipping_postal_code,shipping_state,shipping_city,shipping_address_1,shipping_address_2,coupon_discount_amount
        order_2JGEXf3shdRmUSLVLKiR3U,梶島 充雄,{$today},1111,【BRO】ガイアの眼、グウェナ[JP][緑],1,340,270-1164,千葉県,我孫子市,つくし野3-20,我孫子ビレジ504,0
        order_2JGEXf3shdRmUSLVLKiR3U,梶島 充雄,{$today},1112,【SPM】インポスター症候群[JP][青],1,480,270-1164,千葉県,我孫子市,つくし野3-20,我孫子ビレジ504,0
        order_2JGEXf3shdRmUSLVLKiR3U,梶島 充雄,{$today},1113,【MHR】天空の刃、セラ[JP][赤],1,600,270-1164,千葉県,我孫子市,つくし野3-20,我孫子ビレジ504,0
        order_3KGEXf3shdRmUSLVLKiR4V,田中 一郎,{$today},1114,【KHM】無情な行動[JP][黒],2,720,160-0023,東京都新宿区西新宿2-8-1,新宿モノリスビル1204,0
        order_3KGEXf3shdRmUSLVLKiR4V,田中 一郎,{$today},1115,【ZNR】時の一掃者、テフェリー[JP][青],1,500,160-0023,東京都新宿区西新宿2-8-1,新宿モノリスビル1204,0
        order_3KGEXf3shdRmUSLVLKiR4V,田中 一郎,{$today},1116,【SPM】スパイダーセンス[JP][青],1,500,160-0023,東京都新宿区西新宿2-8-1,新宿モノリスビル1204,0
        CSV;
        $response = $this->upload($content);
        $response->assertJsonCount(2);
        for($i = 0; $i < 2; $i++) {
            $response->assertJsonPath("{$i}.". ShiptConstant::ITEMS, function($items) {
                return count($items) === 3;
            });
        }
        $json = $response->json();
        logger()->debug($json);
    }
    
    public function test_発送日が今日() {
        
    }
    
    public function test_発送日が昨日() {
        
    }
    
    public function test_発送日が明日() {
        
    }
    
    /**
     * 購入者情報をランダムで作成する。
     *
     * @return array
     */
    private function createBuyerInfo():array {
        return [
            SC::ORDER_ID => $this->createOrderId(),
            SC::BUYER => fake()->name(),
            SC::POSTAL_CODE => fake()->postcode1()."-".fake()->postcode2(),
            SC::STATE => fake()->prefecture(),
            SC::CITY => fake()->city(),
            SC::ADDRESS_1 => fake()->streetAddress(),
            SC::ADDRESS_2 => fake()->secondaryAddress(),
        ];
    }

    private function createItemInfo(int $foiltypeId = 1, int $promotypeId = 1):array {
        $stock = Stockpile::inRandomOrder()->
                            where(StockpileHeader::QUANTITY, '>', 0)
                            ->whereHas('cardinfo', function($query) use($foiltypeId, $promotypeId){
                                $query->where(CardConstant::FOIL_ID, $foiltypeId)
                                ->where(CardConstant::PROMO_ID, $promotypeId);
                            })->first();
        return [GC::ID => $stock->id, SC::PRODUCT_NAME => $this->product_name($stock->id),
                    StockpileHeader::QUANTITY => fake()->numberBetween(1, $stock->quantity),
                    SC::PRODUCT_PRICE => fake()->numberBetween(300, 10000), 0];
    }

    /**
     * 在庫情報から商品名を作成する。
     *
     * @param integer $id
     * @return string
     */
    private function product_name(int $id): string {
        $stock = Stockpile::find($id);
        if (!$stock) {
            $this->fail("在庫情報が存在しません。ID: {$id}");
        }
        $card = $stock->cardinfo;
        $exp = $stock->cardinfo->expansion;
        $foil = $stock->cardinfo->foiltype;
        $promo = $stock->cardinfo->promotype;
        return "【{$exp->attr}】".
                ($foil ? "【{$foil->name}】" : "").
                "{$card->name}".
                ($promo ? "≪{$promo->name}≫" : "").
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
