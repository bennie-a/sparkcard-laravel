<?php

namespace Tests\Unit\DB\Arrival;

use Illuminate\Http\Response;
use Tests\TestCase;

use function PHPUnit\Framework\assertTrue;

/**
 * 入荷情報検索のテストケース
 */
class ArrivalLogFetchTest extends TestCase {

    public function setUp(): void
    {
        parent::setUp();
        $this->seed('TruncateAllTables');
        $this->seed('DatabaseSeeder');
        $this->seed('TestExpansionSeeder');
        $this->seed('TestCardInfoSeeder');
        $this->seed('TestStockpileSeeder');
        $this->seed('TestArrivalLogSeeder');
    }

    public function test_全件検索() {
        $query = [];
        $response = $this->json('GET', 'api/arrival', $query);
        $json = $response->json();
        logger()->debug(count($json));
        $response->assertOk();
        // $this->assertNotEmpty($json);
        // foreach($json as $j) {
        //     logger()->debug($j);
        // }
    }
}