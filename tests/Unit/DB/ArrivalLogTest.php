<?php

namespace Tests\Unit\DB;

use App\Models\ArrivalLog;
use App\Models\CardInfo;
use App\Models\Stockpile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotNull;

/**
 * 入荷手続きに関するテスト
 */
class ArrivalLogTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed('TestExpansionSeeder');
        $this->seed('MainColorSeeder');
        $this->seed('ShippingSeeder');
        $this->seed('TestCardInfoSeeder');
        $this->seed('TestStockpileSeeder');
    }
    /**
     * A basic feature test example.
     *
     * @dataProvider okprovider
     * @return void
     */
    public function test_ok(string $attr, string $name, bool $isFoil,  string $condition, string $arrivalDate, int $cost, int $market_price)
    {
        $language = 'JP';
        $info = CardInfo::findSingleCard($attr, $name, $isFoil);
        $before = Stockpile::findSpecificCard($info->id, $language, $condition);
        $quantity = 2;
        $supplier = 'オリパ';
        $response = $this->post('/api/arrival', ['card_id' => $info->id, 'language' => $language, 'condition' => $condition,
                                                                                 'arrival_date' => $arrivalDate, 'cost' => $cost, 'market_price' => $market_price,
                                                                                  'quantity' => $quantity, 'supplier' => $supplier]);

        $after = Stockpile::findSpecificCard($info->id, $language, $condition);
        assertNotNull($after, '在庫情報');
        if (!empty($before)) {
            $quantity += $before->quantity;
        }
        assertEquals($quantity, $after->quantity, '数量');

        $log = ArrivalLog::where('stock_id',  $after->id)->first();
        assertNotNull($log, '入荷ログ');
        assertEquals($cost, $log->cost, '原価');
        assertEquals($supplier, $log->supplier, '仕入れ先');
        assertEquals($arrivalDate, $log->arrival_date, '入荷日');

        $response->assertStatus(Response::HTTP_CREATED);
    }

    /**
     * OKケース
     *
     * @return void
     */
    public function okprovider() {
        return [
            '在庫情報あり' => ['BRO', 'ファイレクシアのドラゴン・エンジン', false, 'NM', '2023-07-24', 23, 200]
        ];
    }

    public function tearDown(): void
    {
        Artisan::call('migrate:refresh');
        parent::tearDown();
    }
}
