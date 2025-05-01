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

        $repo = new CardBoardRepository();
        $noShiptLog = $this->getNoShiptArrivalLog();
        $noShiptPage = $repo->findBySparkcardId($noShiptLog->card_id);

        logger()->debug("入荷情報なしのNotionカード更新:{$noShiptPage->getId()}");
        $page = new Page();
        $page->setId($noShiptPage->getId());
        $page->setNumber(NotionConstant::QTY, $noShiptLog->qty);
        $repo->update($page);
    }

    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $details = [Scon::CARD_NAME => '入荷情報編集カード_出荷情報なし'];
        $logs = ArrivalLog::filtering($details);
        $this->assertNotEmpty($logs, '入荷情報が取得できません。');

        $targetLog = $logs->first();
        $response = $this->delete('/api/arrival/'.$targetLog->arrival_id);
        $response->assertStatus(204);
    }

    /**
     * Undocumented function
     *
     * @return CardInfo
     */
    private function getNoShiptArrivalLog() {
        $collector = new ArrivalLogCollector();
        return $collector->fetchByCardname('入荷情報編集カード_出荷情報なし');
    }

    private function getCardInfo(string $attr, string $cardname) {
         $info = CardInfo::findCardByAttr($attr, $cardname);
         return $info;
    }

    // Notionカードがない
    // Notionカードがある
    // 入荷情報の数量 < 在庫情報の数量
    // 入荷情報の数量 = 在庫情報の数量
    //     1.出荷情報あり
    //    2.出荷情報なし
    // 入荷情報の数量 > 在庫情報の数量

}
