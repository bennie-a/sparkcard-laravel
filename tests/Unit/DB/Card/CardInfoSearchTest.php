<?php

namespace Tests\Unit\DB\Card;

use App\Enum\CardColor;
use Tests\TestCase;
use App\Services\Constant\CardConstant as Con;
use App\Services\Constant\GlobalConstant as GCon;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\Database\Seeders\DatabaseSeeder;
use Tests\Database\Seeders\TestCardInfoSeeder;
use Tests\Database\Seeders\TestStockpileSeeder;
use Tests\Database\Seeders\TruncateAllTables;
use Tests\Trait\GetApiAssertions;

/**
 * カード情報検索に関するテスト
 */
class CardInfoSearchTest extends TestCase
{
    use GetApiAssertions;
    public function setup():void
    {
        parent::setup();
        $this->seed(TruncateAllTables::class);
        $this->seed(DatabaseSeeder::class);
        $this->seed(TestCardInfoSeeder::class);
        $this->seed(TestStockpileSeeder::class);
    }
    /**
     * 在庫情報の有無に関するテスト
     * @return void
     */
    #[DataProvider('stockpileprovider')]
    public function test_stockpile(array $condition, int $quantity)
    {
        $query = [GCon::NAME => $condition[0], Con::SET =>$condition [1],
                                 Con::COLOR => $condition[2], Con::IS_FOIL => $condition[3]];
        $response = $this->assert_OK($query);
        $json = $response->json();
        $this->assertCount(1, $json, '件数');
    }

    public static function stockpileprovider() {
        return [
            '在庫情報なし' => [['在庫情報なし', '', '', false], 0],
            '在庫が0' => [['ドラゴンの運命', '', '', false], 0],
            '在庫が1以上' => [['放浪皇', '', '', false], 1],
            'カード番号に\'s\'が含まれている' => [['', 'XLN', 'W', true], 0],
        ];
    }

    #[TestWith([GCon::NAME], 'カード名')]
    #[TestWith([Con::SET], 'セット名')]
    #[TestWith([Con::COLOR], '色')]
    #[TestDox('検索項目の一部が不足していてもエラーにならないこと')]
    public function test_missing_item(string $param) {
        $query = [GCon::NAME => '放浪', Con::SET =>'NEO', Con::COLOR => 'W', Con::IS_FOIL => false];
        unset($query[$param]);
        array_values($query);

        $response = $this->execute($query);
        $this->assertNotEquals(500, $response->getStatusCode(), 'HTTPステータスコード');
    }

    /**
     * 通常版/特別版のカード名について検証する。
     *
     * @param String $name
     * @return void
     */
    #[DataProvider('promoTypeProvider')]
    public function test_promotype(String $name, int $promotypeId = 1,  String $promoType = '') {
        $query = [GCon::NAME => $name, Con::SET =>'', Con::COLOR => '', Con::IS_FOIL => false];
        $response = $this->assert_OK($query);
        $response->assertOk();

        $json = $response->json();
        $this->assertGreaterThan(0, count($json), '件数');
        foreach ($json as $card) {
            $this->assertEquals($name, $card[GCon::NAME], 'カード名');
            $this->assertArrayHasKey(Con::PROMOTYPE, $card, 'プロモタイプ');
            $promo = $card[Con::PROMOTYPE];
            $this->assertEquals($promotypeId, $promo[GCon::ID], 'プロモタイプID');
            $this->assertEquals($promoType, $promo[GCon::NAME], 'プロモタイプ名');
        }
    }

    public static function promoTypeProvider() {
        return [
            '通常版' => ['発展の暴君、ジン＝ギタクシアス'],
            '特別版' => ['機械の母、エリシュ・ノーン', 23, 'ボーダーレス「胆液」ショーケース'],
        ];
    }

    #[TestWith([false, ''], '通常版')]
    #[TestWith([true, '箔押し'], '箔押し版')]
    #[TestDox('アート・カード検索')]
    public function test_artcard(bool $isFoil, string $foiltype) {
        $query = [GCon::NAME => '', Con::SET => '', Con::COLOR => CardColor::ART->value, Con::IS_FOIL => $isFoil];
        $response = $this->assert_OK($query);
        $response->assertJsonCount(1);
        $response->assertJsonPath('0.color', CardColor::ART->text());
        $response->assertJsonPath('0.foil.is_foil', $isFoil);
        $response->assertJsonPath('0.foil.name', $foiltype);
        $response->assertJsonPath('0.price', 1);
        $response->assertOk();
    }

    /**
     * エンドポイントを取得する。
     *
     * @return string
     */
    protected function getEndPoint():string {
        return 'api/database/card';
    }
}
