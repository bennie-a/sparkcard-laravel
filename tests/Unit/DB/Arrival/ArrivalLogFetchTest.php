<?php

namespace Tests\Unit\DB\Arrival;
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
        assertTrue(true);
    }
}