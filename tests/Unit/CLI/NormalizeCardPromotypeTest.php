<?php

namespace Tests\Unit\CLI;

use Database\Seeders\ExpansionSeeder;
use Database\Seeders\FoiltypeSeeder;
use Database\Seeders\MainColorSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\Database\Seeders\TestExpansionSeeder;
use Tests\Database\Seeders\TestPromotypeSeeder;
use Tests\Database\Seeders\TruncateAllTables;
use Tests\TestCase;

/**
 * normalize:card-promotypeコマンドのテスト
 */
class NormalizeCardPromotypeTest extends TestCase
{

    public function setup(): void
    {
        parent::setUp();
        logger()->info('マスタデータ登録開始');
        $this->seed(TruncateAllTables::class);
        // $this->seed(ExpansionSeeder::class);
        // $this->seed(TestExpansionSeeder::class);
        // $this->seed(MainColorSeeder::class);
        // $this->seed(FoiltypeSeeder::class);
        // $this->seed(TestPromotypeSeeder::class);
        logger()->info('マスタデータ登録終了');
    }
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        // $exitCode  = Artisan::call('normalize:card-promotype');
        // $this->assertEquals(0, $exitCode, 'コマンドの実行に失敗しました。');
    }
}
