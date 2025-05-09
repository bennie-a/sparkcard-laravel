<?php

namespace Tests\Unit\DB\Arrival;

use App\Facades\CardBoard;
use App\Models\ArrivalLog;
use App\Models\CardInfo;
use App\Models\Stockpile;
use App\Repositories\Api\Notion\CardBoardRepository;
use App\Services\Constant\GlobalConstant;
use App\Services\Constant\NotionConstant;
use App\Services\Constant\NotionStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Database\Seeders\Arrival\TestArrivalLogSeeder;
use Tests\Database\Seeders\Arrival\TestArrivalStockpileSeeder;
use Tests\Database\Seeders\Arrival\TestArrLogCardInfoSeeder;
use Tests\Database\Seeders\Arrival\TestArrLogEditSeeder;
use Tests\Database\Seeders\DatabaseSeeder;
use Tests\Database\Seeders\TestExpansionSeeder;
use Tests\Database\Seeders\TruncateAllTables;
use Tests\TestCase;
use App\Services\Constant\SearchConstant as SCon;
use FiveamCode\LaravelNotionApi\Entities\Page;
use FiveamCode\LaravelNotionApi\Notion;
use FiveamCode\LaravelNotionApi\NotionFacade;
use FiveamCode\LaravelNotionApi\Query\Filters\Filter;
use FiveamCode\LaravelNotionApi\Query\Filters\Operators;
use Tests\Database\Collector\ArrivalLogCollector;
use FiveamCode\LaravelNotionApi\Entities\Properties\NumberProperty;

/**
 * 入荷情報削除APIのテストクラス
 */
class ArrivalLogDeleteTest extends TestCase
{
    
    private $noShiptId =  '1df79508-4f9b-80c6-afb8-f06458647242';
    
    private $noStockId = '1ec79508-4f9b-80bf-be55-e2a95e60ad58';
    
    public function setUp(): void
    {
        parent::setUp();
        $this->seed(TruncateAllTables::class);
        $this->seed(DatabaseSeeder::class);
        $this->seed(TestArrLogCardInfoSeeder::class);
        $this->seed(TestArrivalStockpileSeeder::class);
        $this->seed(TestArrLogEditSeeder::class);

        $repo = $this->createCardRepo();

        $noShiptLog = $this->getNoShiptArrivalLog();
    
        $nostockLog = $this->getNoStockArrivalLog();

        $this->updateNotionCard($this->noShiptId, $noShiptLog);
        $this->updateNotionCard($this->noStockId, $nostockLog);
    }

    private function updateNotionCard(string $id, ArrivalLog $log) {
        $repo = $this->createCardRepo();
        $page = new Page();
        $page->setId($id);
        $page->setNumber(NotionConstant::QTY, $log->qty);
        $page->setSelect('Status', NotionStatus::OnSale->value);
        $page->setNumber(NotionConstant::SPARK_ID, $log->card_id);
        return $repo->update($page);
    }

    /**
     * @dataProvider notionProvider
     */
    public function test_Notionカードあり(string $cardName, string $notionId, NotionStatus $status = NotionStatus::OnSale)
    {
        $targetLog = $this->getArrivalLogByCardname($cardName);
        $expectedQty = $this->execute($targetLog);

        $page = $this->findNotionCard($notionId);
        $this->assertNotNull($page, 'Notionカードが見つからない');

        /** @var  NumberProperty $actualQty */
        $actualQty = $page->getProperty(NotionConstant::QTY);
        $this->assertEquals($expectedQty, $actualQty->getNumber(), 'Notionカードの枚数が一致しない');

        /** @var SelectProperty $actualStatus */
        $actualStatus = $page->getProperty('Status');
        $this->assertEquals($status->value, $actualStatus->getName(), 'NotionカードのStatusが一致しない');
        /** @var Tests\Unit\DB\Arrival\NumberProperty $spark_id */
        $spark_id = $page->getProperty(NotionConstant::SPARK_ID);

        if ($expectedQty == 0) {
            $this->assertEquals(0, $spark_id->getNumber(), "sparkcard_idが0でない");
        } else {
            $this->assertNotEquals(0, $spark_id->getNumber(), "sparkcard_idが0である");
        }
    }

    protected function notionProvider(): array
    {
        return [
            '削除後の在庫数が1以上' => [
                'cardName' => '入荷情報編集カード_出荷情報なし',
                'notionId' => $this->noShiptId,
            ],
            '削除後の在庫数が0' => [
                 'cardName' => '入荷情報編集カード_削除後在庫数0',
                'notionId' => $this->noStockId,
                'status' => NotionStatus::Archive,
            ]
        ];
    }

    public function test_Notionカードなし() {
        $targetLog = $this->getArrivalLogByCardname('入荷情報編集カード_Notionカードなし');
        $this->execute($targetLog);
        
        $repo = $this->createCardRepo();
        $page = $repo->findBySparkcardId($targetLog->card_id);
        $this->assertNull($page, 'Notionカードが見つかる');
        
        $details = [
            SCon::STATUS => NotionStatus::Archive->value,
            SCon::PRICE => 0,
        ];
        $pages = $repo->findByStatus($details);
        $this->assertCount(0, $pages, 'Notionカードが見つかる');
    }
    // 入荷情報の数量 < 在庫情報の数量
    // 入荷情報の数量 = 在庫情報の数量
    //     1.出荷情報あり
    //    2.出荷情報なし
    // 入荷情報の数量 > 在庫情報の数量
    /**
     * @dataProvider arrivalLogProvider
     */
    public function test_入荷情報の有無(string $cardName, callable $method) {
        $beforeLog = $this->getArrivalLogByCardname($cardName);
        $expectedQty = $this->execute($beforeLog);

        $afterLog = ArrivalLog::find($beforeLog->id);
        $this->assertNull($afterLog, '入荷情報が削除されていない');

        $stock = Stockpile::where(GlobalConstant::ID, $beforeLog->stock_id)->first();
        $method($stock, $expectedQty);
    }

    public function arrivalLogProvider(): array
    {
        return [
            '出荷情報なし_入荷情報の数量 < 在庫情報の数量' => [
                '入荷情報編集カード_削除後在庫数-1',  $this->assertNoStockpile(),
            ],
            '出荷情報なし_入荷情報の数量 = 在庫情報の数量' => [
                '入荷情報編集カード_削除後在庫数0', $this->assertNoStockpile(),
            ],
            '出荷情報なし_入荷情報の数量 > 在庫情報の数量' => [
                '入荷情報編集カード_出荷情報なし', $this->assertStockpile(),
            ]
        ];
    }

    private function assertStockpile() {
        return function(?Stockpile $stock, int $expectedQty) {
            $this->assertNotNull($stock, '在庫情報が見つからない');
            $this->assertEquals($expectedQty, $stock->quantity, '在庫情報の数量が一致しない');
        };
    }

    private function assertNoStockpile() {
        return function(?Stockpile $stock, int $expectedQty) {
            $this->assertNull($stock, '在庫情報が見つかる');
        };
    }

    private function execute(ArrivalLog $targetLog) {
        $expectedQty = $targetLog->qty - $targetLog->rog;

        $response = $this->delete('/api/arrival/'.$targetLog->id);
        $response->assertStatus(204);
        return $expectedQty;
    }

    /**
     * Undocumented function
     *
     * @return ArrivalLog
     */
    private function getNoShiptArrivalLog():ArrivalLog {
        $collector = new ArrivalLogCollector();
        return $collector->fetchByCardname('入荷情報編集カード_出荷情報なし');
    }
    
    private function getNoStockArrivalLog(): ArrivalLog
    {
        $collector = new ArrivalLogCollector();
        return $collector->fetchByCardname('入荷情報編集カード_削除後在庫数0');
    }

    private function getArrivalLogByCardname(string $cardName): ArrivalLog
    {
        $collector = new ArrivalLogCollector();
        return $collector->fetchByCardname($cardName);
    }

    private function findNotionCard(string $id): ?Page{
        $page = NotionFacade::pages()->find($id);
        return $page;
    }

    private function createCardRepo() {
        return new CardBoardRepository();
    }
}
