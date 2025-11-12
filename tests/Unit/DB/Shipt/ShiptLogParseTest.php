<?php

namespace Tests\Feature\tests\Unit\DB\Shipt;

use App\Enum\CsvFlowType;
use App\Enum\ShopPlatform;
use App\Http\Response\CustomResponse;
use App\Models\CsvHeader;
use Database\Seeders\CsvHeaderSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
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

     public function test_同じ注文番号が複数件(): void
    {
        $order_id1 = $this->createOrderId();
        logger()->debug("order_id1: {$order_id1}");
        $order_id2 = $this->createOrderId();
        logger()->debug("order_id1: {$order_id2}");
    }

    public function test_注文数が1件(): void
    {
        $today = TestDateUtil::formatToday();
        $orderId = $this->createOrderId();
        $buyer = fake()->name();
        $postalCode = fake()->postcode1()."-".fake()->postcode2();
        $pref = fake()->prefecture();
        $city = fake()->city();
        $address1 = fake()->streetAddress();
        $address2 = fake()->secondaryAddress();
        $content = <<<CSV
        {$this->getHeader()}
        {$orderId},{$buyer},{$today},1111,【BRO】ガイアの眼、グウェナ[JP][緑],1,340,{$postalCode},{$pref},{$city},{$address1},{$address2},0
        {$orderId},{$buyer},{$today},1112,【BRO】ガイアの眼、グウェナ[JP][緑],2,480,{$postalCode},{$pref},{$city},{$address1},{$address2},0
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

        CSV;
        $response = $this->upload($content);
        $json = $response->json();
        logger()->debug($json);
    }

    public function test_発送日が今日() {

    }

    public function test_発送日が昨日() {

    }

    public function test_発送日が明日() {

    }

    public function test_ng_ヘッダー不足(): void {
        $content = <<<CSV
        order_id,buyer_name,original_product_id,product_name,quantity,product_price,shipping_postal_code,shipping_state,shipping_city,shipping_address_1,shipping_address_2,coupon_discount_amount
        order_2JGEXf3shdRmUSLVLKiR3U,梶島 充雄,1111,【BRO】ガイアの眼、グウェナ[JP][緑],1,340,270-1164,千葉県,我孫子市,つくし野3-20,我孫子ビレジ504,0
        order_2JGEXf3shdRmUSLVLKiR3U,梶島 充雄,1112,【SPM】インポスター症候群[JP][青],1,480,270-1164,千葉県,我孫子市,つくし野3-20,我孫子ビレジ504,00
        CSV;
        $response = $this->upload($content, CustomResponse::HTTP_CSV_VALIDATION);
        $response->assertJson(function(AssertableJson $json) {
            $json->hasAll(['title', 'status', 'request', 'detail']);
            $json->whereAll([
                'title' => 'ヘッダー不足',
                'status' => CustomResponse::HTTP_CSV_VALIDATION,
                'detail' => 'ヘッダーが足りません: shipping_date',
                'request' => 'api/shipping/parse',
            ]);
        });
    }

    private function upload(String $content, int $status = Response::HTTP_CREATED) {
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
                                'shipping_date',
                                'original_product_id',
                                'product_name',
                                'quantity',
                                'product_price',
                                'shipping_postal_code',
                                'shipping_state',
                                'shipping_city',
                                'shipping_address_1',
                                'shipping_address_2',
                                'coupon_discount_amount',
                            ]);
        return $header;
    }
}
