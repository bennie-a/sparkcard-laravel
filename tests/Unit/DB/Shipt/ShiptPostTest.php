<?php

namespace Tests\Unit\DB\Shipt;

use App\Http\Controllers\ShiptLogController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\Database\Seeders\DatabaseSeeder;
use Tests\Database\Seeders\TestCardInfoSeeder;
use Tests\Database\Seeders\TestStockpileSeeder;
use Tests\Database\Seeders\TruncateAllTables;
use Tests\TestCase;

/**
 * 出荷情報登録のテストクラス
 *
 */
#[CoversClass(ShiptLogController::class)]
class ShiptPostTest extends TestCase
{

    public function setup():void {
        parent::setup();
        $this->seed(TruncateAllTables::class);
        $this->seed(DatabaseSeeder::class);
        $this->seed(TestCardInfoSeeder::class);
        $this->seed(TestStockpileSeeder::class);
    }

    #[Test]
    #[TestDox('出荷情報の登録に成功することを検証する')]
    public function ok(): void
    {
        $request = ShiptLogTestHelper::createStoreRequest();
        $response = $this->post('api/shipping', $request);
        $response->assertStatus(Response::HTTP_CREATED);
    }
}
