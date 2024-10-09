<?php

namespace Tests\Unit\DB;

use App\Facades\CardBoard;
use App\Http\Response\CustomResponse;
use App\Models\ArrivalLog;
use App\Models\CardInfo;
use App\Models\MainColor;
use App\Models\Shipping;
use App\Models\Stockpile;
use App\Repositories\Api\Notion\CardBoardRepository;
use FiveamCode\LaravelNotionApi\Entities\Page;
use FiveamCode\LaravelNotionApi\Entities\Properties\Number;
use FiveamCode\LaravelNotionApi\Exceptions\NotionException;
use Illuminate\Http\Response;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotEquals;
use function PHPUnit\Framework\assertNotNull;
use App\Services\Constant\NotionConstant as JA;

/**
 * 入荷手続きに関するテスト
 */
class ArrivalLogTest extends TestCase
{

    // 事前にNotionからドラゴンのファイレクシア・エンジン[通常]を削除する。
    private $repo;
    public function setUp(): void
    {
        parent::setUp();
        $this->seed('TruncateAllTables');
        $this->seed('DatabaseSeeder');
        $this->seed('TestExpansionSeeder');
        $this->seed('TestCardInfoSeeder');
        $this->repo = new CardBoardRepository();
        $page = $this->repo->findBySparkcardId(3);
        $updatePage = new Page();
        $updatePage->setId($page->getId());
        $updatePage->set(JA::QTY, Number::value(3));
        CardBoard::updatePage($updatePage);

        $storePage = $this->repo->findBySparkcardId(2);
        if (!empty($storePage)) {
            $storePage->setSelect(JA::STATUS, '取引完了');
            CardBoard::updatePage($storePage);
        }
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
        assertEquals($quantity, $after->quantity, JA::QTY);

        $log = ArrivalLog::where('stock_id',  $after->id)->first();
        assertNotNull($log, '入荷ログ');
        assertEquals($cost, $log->cost, '原価');
        assertEquals($supplier, $log->supplier, '仕入れ先');
        assertEquals($arrivalDate, $log->arrival_date, '入荷日');

        // Notion
        try {
            $repo = new CardBoardRepository();
            $card = $repo->findBySparkcardId($info->id);
            assertEquals('日本語', $card->getProperty(JA::LANG)->getName());
            
            $color = MainColor::find($info->color_id);
            assertEquals($color->name, $card->getProperty(JA::COLOR)->getName(), JA::COLOR);
            
            if ($card->getId() === 'b4c3cc34-ca79-4109-b26d-068e3975fd2f') {
                assertNotEquals($market_price, $card->getProperty(JA::PRICE)->getNumber(), 'Notionの価格');
                assertEquals(5, $card->getProperty(JA::QTY)->getNumber(), 'Notionの枚数');
            } else {
                assertEquals('要写真撮影', $card->getProperty(JA::STATUS)->getName(), 'Status');
                assertEquals($market_price, $card->getProperty(JA::PRICE)->getNumber(), 'Notionの価格');
                assertEquals($quantity, $card->getProperty(JA::QTY)->getNumber(), 'Notionの枚数');

                // 発送方法
                $shipt = $card->getProperty(JA::SHIPT)->getContent();
                $shipt_id = str_replace('-', '', $shipt[0]['id']);
                logger()->debug($shipt_id);
                $item = Shipping::findByNotionId($shipt_id);
                assertNotNull($item, '発送方法の有無');
            }
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
            // '在庫情報あり' => ['BRO', 'ファイレクシアのドラゴン・エンジン', true, 'NM', '2023-07-24', 23, 400],
            '在庫情報なし_ミニレター' => ['BRO', 'ドラゴンの運命', false, 'NM', '2023-06-10', 23, 100],
            '在庫情報なし_クリックポスト' => ['NEO', '告別≪ショーケース≫', true, 'NM', '2023-06-10', 23, 1500]
        ];
    }

    /**
     * NGケース(HTTPステータスコードとメッセージを確認する)
     * @dataProvider ngprovider
     * @param integer $cardId
     * @return void
     */
    public function test_ng(int $cardId) {
        $response = $this->post('/api/arrival', ['card_id' => $cardId, 'language' => 'JP', 'condition' => 'NM-',
                                                                                 'arrival_date' => '2023/7/25', 'cost' => 10, 'market_price' => 400,
                                                                                  'quantity' => 3, 'supplier' => '私物']);

        $response->assertStatus(CustomResponse::HTTP_NOT_FOUND_CARD);
    }

    public function ngprovider() {
        return [
            'カード情報IDが存在しない' => [99],
        ];
    }

    public function tearDown(): void
    {
        // Artisan::call('migrate:refresh');
        // $page = $this->repo->findBySparkcardId(3);
        parent::tearDown();
    }
}
