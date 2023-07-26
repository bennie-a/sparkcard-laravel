<?php

namespace Tests\Unit\DB;

use App\Facades\CardBoard;
use App\Models\ArrivalLog;
use App\Models\CardInfo;
use App\Models\Stockpile;
use App\Repositories\Api\Notion\CardBoardRepository;
use FiveamCode\LaravelNotionApi\Entities\Page;
use FiveamCode\LaravelNotionApi\Entities\Properties\Number;
use FiveamCode\LaravelNotionApi\Exceptions\NotionException;
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

        $this->repo = new CardBoardRepository();
        $page = $this->repo->findBySparkcardId(2);
        $updatePage = new Page();
        $updatePage->setId($page->getId());
        $updatePage->set('枚数', Number::value(1));
        \CardBoard::updatePage($updatePage);

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
        $response->assertStatus(Response::HTTP_CREATED);
        // DB
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

        // Notion
        try {
            $repo = new CardBoardRepository();
            $card = $repo->findBySparkcardId($info->id);
            assertEquals($quantity, $card->getProperty('枚数')->getNumber(), 'Notionの枚数');
        } catch(NotionException $e) {
            $this->fail($e->getMessage());
        }
    }

    /**
     * OKケース
     *
     * @return void
     */
    public function okprovider() {
        return [
            '在庫情報あり' => ['BRO', 'ファイレクシアのドラゴン・エンジン', false, 'NM', '2023-07-24', 23, 200],
            // '在庫情報なし' => ['BRO', 'ファイレクシアのドラゴン・エンジン', true, 'EX+', '2023-06-10', 23, 100]
        ];
    }

    public function tearDown(): void
    {
        Artisan::call('migrate:refresh');
        parent::tearDown();
    }
}
