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
    public function test_example(): void
    {
        Storage::fake('local');
        // ダミーCSVファイル作成
        $file = UploadedFile::fake()->create('shipping_test.csv', 10, 'text/csv');

        $response = $this->postJson('/api/shipping/parse', ['file' => $file], [
            'Content-Type' => 'multipart/form-data',
        ]);
        
        $response->assertStatus(Response::HTTP_CREATED);
    }
}
