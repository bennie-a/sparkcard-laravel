<?php

namespace Tests\Unit\DB\Arrival;

use App\Facades\CardBoard;
use App\Http\Response\CustomResponse;
use App\Models\ArrivalLog;
use App\Models\CardInfo;
use App\Models\MainColor;
use App\Models\Shipping;
use App\Models\Stockpile;
use App\Models\VendorType;
use App\Repositories\Api\Notion\CardBoardRepository;
use App\Services\CardBoardService;
use FiveamCode\LaravelNotionApi\Entities\Page;
use FiveamCode\LaravelNotionApi\Entities\Properties\Number;
use FiveamCode\LaravelNotionApi\Exceptions\NotionException;
use Illuminate\Http\Response;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotEquals;
use function PHPUnit\Framework\assertNotNull;
use App\Services\Constant\NotionConstant as JA;
use App\Services\Constant\StockpileHeader as Header;
use App\Services\Constant\ArrivalConstant as ACon;
use App\Services\Constant\NotionStatus;
use App\Services\Constant\SearchConstant as Scon;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Database\Seeders\Arrival\PostStockpileSeeder;
use Tests\Database\Seeders\DatabaseSeeder;
use Tests\Database\Seeders\TestCardInfoSeeder;
use Tests\Database\Seeders\TruncateAllTables;
use Tests\Util\TestDateUtil;

/**
 * 入荷情報登録に関するテスト
 */
class ArrivalLogPostTest extends TestCase
{

    // 事前にNotionからドラゴンのファイレクシア・エンジン[通常]を削除する。
    private $repo;
    public function setUp(): void
    {
        parent::setUp();
        $this->seed(TruncateAllTables::class);
        $this->seed(DatabaseSeeder::class);
        $this->seed(TestCardInfoSeeder::class);
        // $this->seed(PostStockpileSeeder::class);
        $this->repo = new CardBoardRepository();

        // $page = $this->repo->findBySparkcardId(3);
        // $updatePage = new Page();
        // $updatePage->setId($page->getId());
        // $updatePage->set(JA::QTY, Number::value(3));
        // CardBoard::updatePage($updatePage);

        // $storePage = $this->repo->findBySparkcardId(2);
        // if (!empty($storePage)) {
        //     $newPage = new Page();
        //     $newPage->setId($storePage->getId());
        //     $newPage->setSelect(JA::STATUS, NotionStatus::Complete->value);
        //     CardBoard::updatePage($newPage);
        // }
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */
    #[DataProvider('okprovider')]
    public function test_ok(string $attr, string $name, bool $isFoil,  string $condition, string $arrivalDate, int $cost, int $market_price)
    {
        $language = 'JP';
        $info = CardInfo::findSingleCard($attr, $name, $isFoil);
        $before = Stockpile::findSpecificCard($info->id, $language, $condition);
        $quantity = 2;
        $params = [Header::CARD_ID => $info->id, Header::LANGUAGE => $language, Header::CONDITION => $condition,
                              ACon::ARRIVAL_DATE => $arrivalDate, Header::COST => $cost, Header::MARKET_PRICE => $market_price,
                            Header::QUANTITY => $quantity, SCon::VENDOR_TYPE_ID => 1];
        $log = $this->execute($params);

        // DB
        $after = Stockpile::findSpecificCard($info->id, $language, $condition);
        assertNotNull($after, '在庫情報');
        if (!empty($before)) {
            $quantity += $before->quantity;
        }
        assertEquals($quantity, $after->quantity, JA::QTY);

        assertNotNull($log, '入荷ログ');
        assertEquals($cost, $log->cost, '原価');
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
    public static function okprovider() {
        return [
            '在庫情報あり' => ['BRO', 'ファイレクシアのドラゴン・エンジン', true, 'NM', '2023-07-24', 23, 400],
            // '在庫情報なし_ミニレター' => ['BRO', 'ドラゴンの運命', false, 'NM', '2023-06-10', 23, 100],
            // '在庫情報なし_クリックポスト' => ['NEO', '告別≪ショーケース≫', true, 'NM', '2023-06-10', 23, 1500]
        ];
    }

    /**
     * 既に在庫あり/なしでの登録テスト
     *
     * @return void
     */
    #[DataProvider('stockpileProvider')]
    public function test_ok_stockpile(int $cardId) {
        $params = $this->createParams($cardId, TestDateUtil::formatToday());
        $log = $this->execute($params);

    }

    public static function stockpileProvider() {
        return [
            '在庫なし_stockpileテーブルにレコードなし' =>[13],
            // '在庫なし_stockpileテーブルにレコードあり' =>[],
            // '在庫あり' =>[
            //     'BRO', 'ドラゴンの運命', false, 'NM', '2023-07-24', 23, 400
            // ],
        ];
    }

    //DB
        // 通常版
        // 特別版
    //Notion
        // 在庫情報なし
        // 在庫情報あり
        // 価格が1500未満
        // 価格が1500以上

    /**
     * 入荷先に関するテスト
     *
     * @param integer $vender_type_id
     * @param string $vendor
     * @return void
     */
    #[DataProvider('vendorprovider')]
    public function test_vendor(int $vendor_type_id, string $vendor) {
        $info = CardInfo::findSingleCard('BRO', 'ドラゴンの運命', false);

        $params = [Header::CARD_ID => $info->id, Header::LANGUAGE => 'JP', Header::CONDITION => 'NM',
        ACon::ARRIVAL_DATE => '2024/10/11', Header::COST => 22, Header::MARKET_PRICE => 400,
                            Header::QUANTITY => 1, SCon::VENDOR_TYPE_ID => $vendor_type_id, ACon::VENDOR => $vendor];
        Mockery::mock(CardBoardService::class)->shouldReceive('store')->with(Mockery::any(), [])->andReturn();
        $log = $this->execute($params);

        // 出荷先カテゴリ
        assertEquals($vendor_type_id, $log->vendor_type_id, '入荷先カテゴリ');
        assertEquals($vendor, $log->vendor, '入荷先名');
        Mockery::close();
    }

    public static function vendorprovider() {
        return [
            'オリジナルパック' =>[1, ''],
            '私物' =>[2, ''],
            '買取' =>[3, '駿河屋'],
            '棚卸し' =>[4, ''],
            '返品' =>[5, ''],
        ];
    }

    private function execute(array $params) {
        $response = $this->post('/api/arrival', $params);
        $response->assertStatus(Response::HTTP_CREATED);
        $json = $response->json();
        $log = ArrivalLog::find($json['arrival_id']);
        assertNotNull($log, '入荷ログ');
        return $log;
    }

    /**
     * NGケース(HTTPステータスコードとメッセージを確認する)
     * @param integer $cardId
     * @return void
     */
    #[DataProvider('ngprovider')]
    public function test_ng(int $cardId) {
        $response = $this->post('/api/arrival', ['card_id' => $cardId, 'language' => 'JP', 'condition' => 'NM-',
                                                                                 'arrival_date' => '2023/7/25', 'cost' => 10, 'market_price' => 400,
                                                                                  'quantity' => 3, 'vendor' => '', 'vendor_type_id' => 2]);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public static function ngprovider() {
        return [
            'カード情報IDが存在しない' => [9999],
        ];
    }

    private function createParams(int $cardId, string $arrivalDate): array{
        $lang = fake()->randomElement(['JP', 'EN', 'IT', 'CT', 'CS']);
        $quantity = fake()->numberBetween(1, 5);
        $cost  = fake()->numberBetween(10, 200);
        $market_price = fake()->numberBetween(100, 5000);
        $condition = fake()->randomElement(['NM', 'EX', 'NM-', 'EX+', 'PLD']);

        $params = [Header::CARD_ID => $cardId, Header::LANGUAGE => $lang, Header::CONDITION => $condition,
                              ACon::ARRIVAL_DATE => $arrivalDate, Header::COST => $cost, Header::MARKET_PRICE => $market_price,
                            Header::QUANTITY => $quantity, SCon::VENDOR_TYPE_ID => 1];
        return $params;

    }

    public function tearDown(): void
    {
        // Artisan::call('migrate:refresh');
        // $page = $this->repo->findBySparkcardId(3);
        parent::tearDown();
    }
}
