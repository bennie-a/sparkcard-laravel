<?php

namespace Tests\Unit\DB;

use App\Enum\CardColor;
use App\Models\CardInfo;
use App\Models\Stockpile;
use App\Services\Constant\CardConstant as Con;
use App\Services\Constant\GlobalConstant as GCon;
use App\Services\Constant\SearchConstant as SCon;
use App\Services\Constant\StockpileHeader as Header;
use Illuminate\Http\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\Database\Seeders\DatabaseSeeder;
use Tests\Database\Seeders\TestCardInfoSeeder;
use Tests\Database\Seeders\TestStockpileSeeder;
use Tests\Database\Seeders\TruncateAllTables;
use Tests\TestCase;
use Tests\Trait\CardJsonHelper;

/**
 * 在庫管理情報検索のテスト
 */
class StockpileSearchTest extends TestCase
{
    use CardJsonHelper;
    /**
     * テスト開始時のみテストデータを導入する。
     *
     * @return void
     */
    public function setup():void {
        parent::setup();
        $this->seed(TruncateAllTables::class);
        $this->seed(DatabaseSeeder::class);
        $this->seed(TestCardInfoSeeder::class);
        $this->seed(TestStockpileSeeder::class);
    }
    /**
     * 在庫情報検索OKパターン
     *
     * @param string $cardname
     * @param string $setname
     * @param integer $limit
     * @return void
     */
    #[DataProvider('searchprovider')]
    public function test_index_ok(string $cardname, string $setname, int $limit, array $exStockNo) {
        $query = [SCon::CARD_NAME => $cardname, SCon::SET_NAME => $setname, SCon::LIMIT => $limit];
        $response = $this->call('GET', '/api/stockpile', $query);
        $response->assertOk();
        $response->assertJsonCount(count($exStockNo));

        // 検索結果検証
        $expected = Stockpile::findMany($exStockNo)->map(function ($stock) {
            $card = CardInfo::getDetailsById($stock->card_id);
            return [
                GCon::ID => $stock->id,
                Header::LANG => $stock->language,
                Header::QUANTITY => $stock->quantity,
                Header::CONDITION => $stock->condition,
                GCon::UPDATED_AT => $stock->updated_at,
                GCon::CARD => $this->buildCardJson($card)
            ];
        })->values()->toArray();

        $response->assertJson($expected);
    }

    public static function searchprovider() {
        return [
            '検索結果あり_検索条件なし' => ['', '', 0, range(1, 14)],
            '検索結果あり_カード名入力' => ['ファイレクシアの', '', 0, [1,2]],
            '検索結果あり_セット名入力' => ['', 'ONE', 0, [4,5, 12, 13, 14]],
            '検索結果あり_取得件数あり' => ['', '', 2, [1,2]],
            '検索結果あり_通常版' => ['ドロスの魔神', '', 0, [4, 13]],
            '検索結果あり_特別版' => ['機械の母、エリシュ・ノーン', '', 0, [14]],
        ];
    }

    #[TestDox('アートカードの検索')]
    public function test_artcard() {
        $query = [SCon::CARD_NAME => '放浪皇', SCon::SET_NAME => 'NEO', SCon::LIMIT => 0];
        $response = $this->call('GET', '/api/stockpile', $query);
        $response->assertOk();
        $response->assertJsonCount(2);

        foreach ($response->json() as $data) {
            $this->assertEquals('放浪皇', $data[GCon::CARD][GCon::NAME], 'カード名が一致しない');
            $this->assertEquals(CardColor::ART->text(), $data[GCon::CARD][Con::COLOR], 'アートカードの色IDが一致しない');
        }
        $response->assertJsonPath('0.card.foil', ['is_foil' => true, 'name' => '箔押し'], '箔押し版のfoiltypeが一致しない');
        $response->assertJsonPath('1.card.foil', ['is_foil' => false, 'name' => ''], '通常版のfoiltypeが一致しない');
    }

    /**
     * 在庫情報検索NGパターン
     *
     * @return void
     */
    public function test_ng_not_found() {
        $query = [SCon::CARD_NAME => 'xxxx', SCon::SET_NAME => '', 'limit' => 0];
        $response = $this->call('GET', '/api/stockpile', $query);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $response->assertJson([
            'title' => '結果なし',
            'status' => Response::HTTP_NOT_FOUND,
            'detail' => '検索結果がありません。',
            "request" => "api/stockpile"
        ]);
    }
}
