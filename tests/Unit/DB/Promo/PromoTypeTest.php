<?php

namespace Tests\Unit\DB\Promo;

use App\Models\Stockpile;
use App\Services\Constant\StockpileHeader;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\Database\Seeders\DatabaseSeeder;
use Tests\Database\Seeders\TestExpansionSeeder;
use Tests\Database\Seeders\TruncateAllTables;
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
        $this->seed(TruncateAllTables::class);
        $this->seed(DatabaseSeeder::class);
    }

    /**
     * A basic feature test example.
     */
    public function test_プロモタイプあり(): void
    {
        $response = $this->assert_OK([StockpileHeader::SETCODE => 'MH3']);
        $response->assertJsonCount(14);
    }

    public function test_プロモタイプなし(): void
    {
        $response = $this->assert_OK([StockpileHeader::SETCODE => 'MH1']);
        $response->assertJsonCount(12);
    }


    public function test_エキスパンション未登録() {
        $this->assert_NG([StockpileHeader::SETCODE => 'XXX'],
                     Response::HTTP_NOT_FOUND, '指定したエキスパンションが見つかりません: XXX');
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
