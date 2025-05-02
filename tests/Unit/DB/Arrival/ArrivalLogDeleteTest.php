<?php

namespace Tests\Unit\DB\Arrival;

use App\Facades\CardBoard;
use App\Models\ArrivalLog;
use App\Models\CardInfo;
use App\Repositories\Api\Notion\CardBoardRepository;
use App\Services\Constant\NotionConstant;
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
use Tests\Database\Collector\ArrivalLogCollector;

/**
 * 入荷情報削除APIのテストクラス
 */
class ArrivalLogDeleteTest extends TestCase
{

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
        $noShiptPage = $repo->findBySparkcardId($noShiptLog->card_id);

        logger()->debug("入荷情報なしのNotionカード更新:{$noShiptPage->getId()}");
        $page = new Page();
        $page->setId($noShiptPage->getId());
        $page->setNumber(NotionConstant::QTY, $noShiptLog->qty);
        $repo->update($page);
    }

    /**
     * @dataProvider notionProvider
     */
    public function test_入荷情報削除後のNotionカード(string $cardName, bool $expectPageExists)
    {
        $targetLog = $this->getArrivalLogByCardname($cardName);
        $expectedQty = $this->execute($targetLog);

        $page = $this->findNotionCard($targetLog);

        if ($expectPageExists) {
            $this->assertNotNull($page, 'Notionカードが見つからない');
            $actualQty = $page->getProperty(NotionConstant::QTY)->getNumber();
            $this->assertEquals($expectedQty, $actualQty, 'Notionカードの枚数が一致しない');
        } else {
            $this->assertNull($page, 'Notionカードが見つかる');
        }
    }

    protected function notionProvider(): array
    {
        return [
            'Notionカードあり' => [
                'cardName' => '入荷情報編集カード_出荷情報なし',
                'expectPageExists' => true,
            ],
            'Notionカードなし' => [
                'cardName' => '入荷情報編集カード_Notionカードなし',
                'expectPageExists' => false,
            ],
        ];
    }

    public function execute(ArrivalLog $targetLog) {
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
    
    private function getArrivalLogByCardname(string $cardName): ArrivalLog
    {
        $collector = new ArrivalLogCollector();
        return $collector->fetchByCardname($cardName);
    }

    private function findNotionCard(ArrivalLog $log) {
        $repo = $this->createCardRepo();
        $page = $repo->findBySparkcardId($log->card_id);
        return $page;
    }

    // Notionカードがない
    // Notionカードがある
    // 入荷情報の数量 < 在庫情報の数量
    // 入荷情報の数量 = 在庫情報の数量
    //     1.出荷情報あり
    //    2.出荷情報なし
    // 入荷情報の数量 > 在庫情報の数量
    private function createCardRepo() {
        return new CardBoardRepository();
    }
}
