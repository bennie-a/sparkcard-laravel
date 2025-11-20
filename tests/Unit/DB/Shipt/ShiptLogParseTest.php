<?php

namespace Tests\Feature\tests\Unit\DB\Shipt;

use App\Models\Stockpile;
use App\Services\Constant\GlobalConstant as GC;
use App\Services\Constant\ShiptConstant;
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

     public function test_belongTo(): void
    {
        $stock = Stockpile::find(99);
        $this->assertNull($stock);
        // $this->assertNotNull($stock->cardinfo);
        // logger()->info("Card Name: ".$stock->cardinfo->name);

        // $exp = $stock->cardinfo->expansion;
        // $this->assertNotNull($exp);
        // logger()->info("Expansion Name: ".$exp->attr);

        // $this->assertNotNull($stock->cardinfo->foiltype);
        // logger()->info("Foil Type: ".$stock->cardinfo->foiltype->name);

        // $this->assertNotNull($stock->cardinfo->promotype);
        // logger()->info("Promo Type: ".$stock->cardinfo->promotype->name);

    }

    public function test_注文数が1件(): void
    {
        $buyerInfo = $this->createBuyerInfo();
        $today = TestDateUtil::formatToday();
        $csvline1 = array_values($buyerInfo);
        array_push($csvline1, $today, 3, $this->product_name(3), 1, 340, 0);
        $implode = implode(',', $csvline1);
        $content = <<<CSV
        {$this->getHeader()}
        {$implode}
        CSV;
        $response = $this->upload($content, 201);
        $json = $response->json();
        logger()->debug($json);
    }

    /**
     * テスト用購入者情報を作成する。
     */
    private function createBuyerInfo():array {
        return [
            ShiptConstant::ORDER_ID => $this->createOrderId(),
            ShiptConstant::BUYER => fake()->name(),
            ShiptConstant::POSTAL_CODE => fake()->postcode1()."-".fake()->postcode2(),
            ShiptConstant::STATE => fake()->prefecture(),
            ShiptConstant::CITY => fake()->city(),
            ShiptConstant::ADDRESS_1 => fake()->streetAddress(),
            ShiptConstant::ADDRESS_2 => fake()->secondaryAddress(),
        ];
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

    private function getHeader() {
        $header = implode(',', [
                                'order_id',
                                'buyer_name',
                                'shipping_postal_code',
                                'shipping_state',
                                'shipping_city',
                                'shipping_address_1',
                                'shipping_address_2',
                                'shipping_date',
                                'original_product_id',
                                'product_name',
                                'quantity',
                                'product_price',
                                'coupon_discount_amount',
                            ]);
        return $header;
    }
}
