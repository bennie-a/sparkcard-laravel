<?php

namespace Tests\Unit\DB\Arrival;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * 入荷情報削除APIのテストクラス
 */
class ArrivalLogDeleteTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->seed('TruncateAllTables');
        $this->seed('TestExpansionSeeder');
        $this->seed('DatabaseSeeder');
        $this->seed('TestCardInfoSeeder');
        $this->seed('TestStockpileSeeder');
        $this->seed('TestArrivalLogSeeder');
    }

    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->delete('/api/arrival/1');

        $response->assertStatus(204);
    }
}
