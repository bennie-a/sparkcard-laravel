<?php

namespace Tests\Unit\DB\Arrival;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Services\Constant\ArrivalConstant as ACon;
use App\Services\Constant\StockpileHeader as Header;
use App\Services\Constant\SearchConstant as SCon;
use Tests\Database\Seeders\Arrival\TestArrivalStockpileSeeder;
use Tests\Database\Seeders\Arrival\TestArrLogCardInfoSeeder;
use Tests\Database\Seeders\Arrival\TestArrLogEditSeeder;
use Tests\Database\Seeders\DatabaseSeeder;
use Tests\Database\Seeders\TruncateAllTables;

class ArrivalLogUpdateTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(TruncateAllTables::class);
        $this->seed(DatabaseSeeder::class);
        $this->seed(TestArrLogCardInfoSeeder::class);
        $this->seed(TestArrivalStockpileSeeder::class);
        $this->seed(TestArrLogEditSeeder::class);
    }

    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->put('/api/arrival/1', [
            Header::COST => fake()->randomNumber(3),
            Scon::VENDOR_TYPE_ID => 1,
            // 'alog_quan' => 5,
            // 'language' => 'en',
            // 'condition' => 'new',
        ]);

        $response->assertStatus(200);
    }
}
