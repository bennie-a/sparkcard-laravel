<?php

namespace Tests\Feature\tests\Unit\DB\Shipt;

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

    /**
     * A basic feature test example.
     */
    public function test_読み込みテスト(): void
    {
        $today = TestDateUtil::formatToday();
        $content = <<<CSV
        order_id,buyer_name,shipping_date,original_product_id,product_name,quantity,product_price,billing_postal_code,billing_state,billing_city,billing_address_1,billing_address_2,coupon_discount_amount
        order_2JGEXf3shdRmUSLVLKiR3U,梶島 充雄,{$today},1111,【BRO】ガイアの眼、グウェナ[JP][緑],1,340,270-1164,千葉県,我孫子市,つくし野3-20,我孫子ビレジ504,0
        order_2JGEXf3shdRmUSLVLKiR3U,梶島 充雄,{$today},1112,【SPM】インポスター症候群[JP][青],1,480,270-1164,千葉県,我孫子市,つくし野3-20,我孫子ビレジ504,0
        CSV;
        $response = $this->upload($content);
        logger()->info($response->getContent());
    }

    public function test_ng_ヘッダ不足(): void {
                $content = <<<CSV
        order_id,buyer_name,shipping_date,original_product_id,product_name,quantity,product_price,billing_postal_code,billing_state,billing_city,billing_address_1,shipping_address_2,coupon_discount_amount
        order_2JGEXf3shdRmUSLVLKiR3U,梶島 充雄,,,【BRO】ガイアの眼、グウェナ[JP][緑],1,340,270-1164,千葉県,我孫子市,つくし野3-20,我孫子ビレジ504,0
        order_2JGEXf3shdRmUSLVLKiR3U,梶島 充雄,,,【SPM】インポスター症候群[JP][青],1,480,270-1164,千葉県,我孫子市,つくし野3-20,我孫子ビレジ504,00
        CSV;
        $response = $this->upload($content, Response::HTTP_BAD_REQUEST);
        $response->assertJsonFragment([
            'status' => 'CSV Validation Error',
            'error' => 'CSVファイルのヘッダーが足りません: billing_address_2'
        ]);
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
}
