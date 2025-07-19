<?php

namespace Tests\Unit\DB;

use App\Enum\CardColor;
use App\Models\CardInfo;
use App\Models\Expansion;
use App\Models\MainColor;
use App\Models\Stockpile;
use App\Services\Constant\CardConstant;
use App\Services\Constant\GlobalConstant as GCon;
use App\Services\Constant\SearchConstant;
use App\Services\Constant\StockpileHeader as Header;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Database\Seeders\DatabaseSeeder;
use Tests\Database\Seeders\TestCardInfoSeeder;
use Tests\Database\Seeders\TestStockpileSeeder;
use Tests\Database\Seeders\TruncateAllTables;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertJson;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertTrue;

/**
 * 在庫管理情報検索のテスト
 */
class StockpileSearchTest extends TestCase
{
    // use DatabaseTransactions;
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
        $query = ['card_name' => $cardname, 'set_name' => $setname, 'limit' => $limit];
        $response = $this->call('GET', '/api/stockpile', $query);
        $response->assertOk();
        $response->assertJsonCount(count($exStockNo));
        logger()->debug($response->json());

        // 検索結果検証
        $expected = Stockpile::findMany($exStockNo)->map(function ($stock) {
            $card = CardInfo::getDetailsById($stock->card_id);
            return [
                GCon::ID => $stock->id,
                Header::LANG => $stock->language,
                Header::QUANTITY => $stock->quantity,
                GCon::UPDATED_AT => $stock->updated_at,
                GCon::CARD => [
                    GCon::ID => $card->id,
                    GCon::NAME => $card->name,
                    CardConstant::EXP => [
                        GCon::NAME => $card->exp_name,
                        CardConstant::ATTR => $card->exp_attr,
                    ],
                    CardConstant::NUMBER => $card->number,
                    CardConstant::COLOR => CardColor::tryFrom($card->color_id)->text(),
                    CardConstant::IMAGE_URL => $card->image_url,
                    Header::CONDITION => $stock->condition,
                    'foil' => [
                        'is_foil' => $card->isFoil,
                        GCon::NAME => $card->foiltype == '通常版' ? '' : $card->foiltype
                    ],
                    CardConstant::PROMOTYPE => [
                        GCon::ID => $card->promotype_id,
                        GCon::NAME => $card->promo_name,
                    ],
                ],
            ];
        })->values()->toArray();

        $response->assertJson($expected);
    }
    
    public static function searchprovider() {
        return [
            '検索結果あり_検索条件なし' => ['', '', 0, range(1, 12)],
            '検索結果あり_カード名入力' => ['ファイレクシアの', '', 0, [1,2]],
            '検索結果あり_セット名入力' => ['', '統一', 0, [4,5, 10]],
            '検索結果あり_取得件数あり' => ['', '', 2, [1,2]],
            '検索結果あり_通常版' => ['ドロスの魔神', '', 0, [4]],
            '検索結果あり_特別版' => ['機械の母、エリシュ・ノーン', '', 0, [10]],
        ];
    }
    /**
     * 在庫情報検索NGパターン
     * 
     * @return void
     */
    public function test_ng_not_found() {
        $query = [SearchConstant::CARD_NAME => 'xxxx', SearchConstant::SET_NAME => '', 'limit' => 0];
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
