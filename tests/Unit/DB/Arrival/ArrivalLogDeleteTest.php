<?php

namespace Tests\Unit\DB\Arrival;

use App\Models\ArrivalLog;
use App\Models\ShippingLog;
use App\Models\Stockpile;
use App\Repositories\Api\Notion\CardBoardRepository;
use App\Services\Constant\GlobalConstant;
use App\Services\Constant\NotionConstant;
use App\Services\Constant\NotionStatus;
use Tests\Database\Seeders\Arrival\TestArrivalStockpileSeeder;
use Tests\Database\Seeders\Arrival\TestArrLogCardInfoSeeder;
use Tests\Database\Seeders\Arrival\TestArrLogEditSeeder;
use Tests\Database\Seeders\DatabaseSeeder;
use Tests\Database\Seeders\TruncateAllTables;
use Tests\TestCase;
use App\Services\Constant\SearchConstant as SCon;
use FiveamCode\LaravelNotionApi\Entities\Page;
use FiveamCode\LaravelNotionApi\NotionFacade;
use Tests\Database\Collector\ArrivalLogCollector;
use FiveamCode\LaravelNotionApi\Entities\Properties\NumberProperty;
use Illuminate\Testing\TestResponse;

/**
 * 入荷情報削除APIのテストクラス
 */
class ArrivalLogDeleteTest extends TestCase
{
    
    private $noShiptId =  '1df79508-4f9b-80c6-afb8-f06458647242';
    
    private $noStockId = '1ec79508-4f9b-80bf-be55-e2a95e60ad58';
    
    private $inStockName = '入荷情報編集カード_削除後在庫数1以上';

    private $noStockName = '入荷情報編集カード_削除後在庫数0';

    private $minusStockName = '入荷情報編集カード_削除後在庫数-1';

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
                'cardName' => $this->inStockName,
                'notionId' => $this->noShiptId,
            ],
            '削除後の在庫数が0' => [
                 'cardName' => $this->noStockName,
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
    /**
     * @dataProvider arrivalLogProvider
     */
    public function test_在庫情報に出荷情報の紐づけなし(string $cardName, callable $method) {
        $beforeLog = $this->getArrivalLogByCardname($cardName);
        $expectedQty = $this->execute($beforeLog);

        $this->assertArrivalLog($beforeLog);

        $stock = $this->getStockInfo($beforeLog);
        $method($stock, $expectedQty);
    }

    protected function arrivalLogProvider(): array
    {
        return [
            '入荷情報の数量 > 在庫情報の数量' => [
                $this->minusStockName,  $this->assertNoStockpile(),
            ],
            '入荷情報の数量 = 在庫情報の数量' => [
                $this->noStockName, $this->assertNoStockpile(),
            ],
            '入荷情報の数量 < 在庫情報の数量' => [
                $this->inStockName, $this->assertStockpile(),
            ]
        ];
    }

    /**
     * 出荷情報がある場合、削除できるか検証する。
     * @dataProvider linkingShiptLogProvider
     */
    public function test_在庫情報に出荷情報の紐づけあり(string $cardName) {
        $targetLog = $this->getArrivalLogByCardname($cardName);
        $stockid = $targetLog->stock_id;
        ShippingLog::factory()->create(['stock_id' => $stockid]);
        $expectedQty = $this->execute($targetLog);

        $this->assertArrivalLog($targetLog);
        $stock = $this->getStockInfo($targetLog);
        $method = $this->assertStockpile();
        $method($stock, $expectedQty);
    }

    protected function linkingShiptLogProvider(): array
    {
        return [
            '入荷情報の数量 > 在庫情報の数量' => [$this->minusStockName],
            '入荷情報の数量 = 在庫情報の数量' => [$this->noStockName],
            '入荷情報の数量 < 在庫情報の数量' => [$this->inStockName]
        ];
    }

    public function test_削除対象のログが存在しない() {
        $this->deleteByAPI(999)
            ->assertStatus(404)->assertJson([
                'detail' => '指定した情報がありません。',
            ]);
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
        if ($expectedQty < 0) {
            $expectedQty = 0;
        }

        $response = $this->deleteByAPI($targetLog->id);
        $response->assertStatus(204);
        return $expectedQty;
    }

    /**
     * APIを使用して入荷情報を削除する。
     *
     * @param integer $id
     * @return TestResponse
     */
    private function deleteByAPI(int $id): TestResponse{
        return $this->delete('/api/arrival/'.$id);
    }

    /**
     * 入荷情報が削除されているか検証する。
     *
     * @param ArrivalLog $targetLog
     * @return void
     */
    private function assertArrivalLog(ArrivalLog $targetLog) {
        $afterLog = ArrivalLog::find($targetLog->id);
        $this->assertNull($afterLog, '入荷情報が削除されていない');
    }

    /**
     * Undocumented function
     *
     * @return ArrivalLog
     */
    private function getNoShiptArrivalLog():ArrivalLog {
        $collector = new ArrivalLogCollector();
        return $collector->fetchByCardname($this->inStockName);
    }
    
    private function getNoStockArrivalLog(): ArrivalLog
    {
        $collector = new ArrivalLogCollector();
        return $collector->fetchByCardname($this->noStockName);
    }

    private function getArrivalLogByCardname(string $cardName): ArrivalLog
    {
        $collector = new ArrivalLogCollector();
        return $collector->fetchByCardname($cardName);
    }

    private function getStockInfo(ArrivalLog $log): ?Stockpile{
        $stock = Stockpile::where(GlobalConstant::ID, $log->stock_id)->first();
        return $stock;
    }

    private function findNotionCard(string $id): ?Page{
        $page = NotionFacade::pages()->find($id);
        return $page;
    }

    private function createCardRepo() {
        return new CardBoardRepository();
    }
}
