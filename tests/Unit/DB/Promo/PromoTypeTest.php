<?php

namespace Tests\Unit\DB\Promo;

use App\Models\Stockpile;
use App\Services\Constant\StockpileHeader;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tests\Trait\GetApiAssertions;

/**
 * 特別版に関するテストケース
 */
class PromoTypeTest extends TestCase
{
    use GetApiAssertions;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed('TruncateAllTables');
        $this->seed('TestExpansionSeeder');
        $this->seed('DatabaseSeeder');
    }

    /**
     * A basic feature test example.
     */
    public function test_ok(): void
    {
        $response = $this->assert_OK([StockpileHeader::SETCODE => 'MH3']);
    }

    public function test_NoResult() {
        $this->assert_NG([StockpileHeader::SETCODE => 'XXX'],
                     Response::HTTP_NOT_FOUND, '検索結果がありません。');
    }

    /**
     * エンドポイントを取得する。
     *
     * @return string
     */
    protected function getEndPoint():string {
        return  'api/promo';
    }
}
