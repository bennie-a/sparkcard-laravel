<?php

namespace Tests\Feature;

use App\Enum\CardColor;
use App\Http\Controllers\ScryfallController;
use App\Services\Constant\CardConstant as Con;
use Tests\TestCase;
use App\Services\Constant\GlobalConstant as GCon;
use App\Services\Constant\StockpileHeader as Header;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\Database\Seeders\DatabaseSeeder;
use Tests\Database\Seeders\TruncateAllTables;
use Tests\Trait\ApiErrorAssertions;

#[TestDox('エンドポイントが"api/scryfall"のテストクラス')]
#[CoversMethod(ScryfallController::class, 'index')]
class ScryfallTest extends TestCase
{

    use ApiErrorAssertions;
    public function setUp(): void
    {
        parent::setUp();
        $this->seed(TruncateAllTables::class);
        $this->seed(DatabaseSeeder::class);
    }

    #[Test]
    #[TestDox('言語を指定してカード情報が取得できることを検証する')]
    #[TestWith(["ja", '辺境地の御目付役、アジャニ', '8/a/8afd2b17-a53f-421a-a61e-2957973cf860.png?1770090933'], '日本語')]
    #[TestWith(["en", '', '6/1/6124a691-ae83-4d22-a177-0aee65b47064.png?1767951721'], '英語')]
    public function 言語を指定(string $lang, string $jpname, string $imageUrl) {
        $response = $this->ok('ECL', '4', $lang);
        $response->assertJson([
            Con::SET => 'ECL',
            GCon::NAME => $jpname,
            Con::EN_NAME => 'Ajani, Outland Chaperone',
            Con::IMAGE_URL => $this->createImageUrl($imageUrl),
            Con::NUMBER => '4',
        ]);
    }

    #[Test]
    #[TestWith(['ECL', '4', 0], 'multiverseidなし')]
    #[TestWith(['WAR', '272', 463894], 'multiverseidあり')]
    #[TestDox('レスポンスのmultiverseIdの有無を検証する')]
    public function multiverseId(string $setcode, string $number, int $multiverseId) {
        $response = $this->ok($setcode, $number, 'ja');
        $response->assertJsonPath(Con::MULTIVERSEID, $multiverseId);
    }

    #[Test]
    #[TestWith(['1', CardColor::LESS], '無色')]
    #[TestWith(['4', CardColor::WHITE], '白')]
    #[TestWith(['46', CardColor::BLUE], '青')]
    #[TestWith(['88', CardColor::BLACK], '黒')]
    #[TestWith(['159', CardColor::RED], '赤')]
    #[TestWith(['86', CardColor::BLACK], '色付きアーティファクト')]
    #[TestWith(['164', CardColor::GREEN], '緑')]
    #[TestWith(['209', CardColor::MULTI], '多色')]
    #[TestWith(['253', CardColor::ARTIFACT], '伝説のアーティファクト')]
    #[TestWith(['254', CardColor::ARTIFACT], '色無しアーティファクト')]
    #[TestWith(['262', CardColor::LAND], '二色土地')]
    #[TestWith(['264', CardColor::LAND], '無色土地')]
    #[TestWith(['269', CardColor::LAND], '基本土地')]
    #[TestDox('レスポンスのcolorの有無を検証する')]
    public function color(string $number, CardColor $excolor) {
        $response = $this->ok('ECL', $number, 'en');
        $response->assertJsonPath(Con::COLOR, $excolor->value);
    }

    #[Test]
    #[TestDox('レスポンスにpromotypeが存在するか検証する。')]
     public function promotype() {
        $response = $this->ok('EOE', '1', 'en');
        $response->assertJsonPath(Con::PROMOTYPE, 1);
     }

    #[Test]
    #[TestWith(['ONE', '1', ['通常版', 'Foil']], '通常版')]
    #[TestWith(['STA', '125', ['通常版', 'Foil', 'エッチングFoil']], '特殊Foil')]
    #[TestDox('レスポンスにfoiltypeが存在するか検証する。')]
     public function foiltype(string $setcode, string $number, array $foiltypes) {
        $response = $this->ok($setcode, $number, 'ja');
        $response->assertJsonPath(Con::FOIL_TYPE, $foiltypes);
     }
     /**
     * 画像URLが正しいことを検証する。
     *
     * @param string $setcode
     * @param int $number
     * @param string $url
     * @return void
     */
    #[Test]
    #[DataProvider('imageProvider')]
    #[TestDox('レスポンスの画像URLが正しいことを検証する。')]
    public function imageurl(string $setcode, int $number, string $url): void
    {
        $query = [Header::SETCODE => $setcode, Con::NUMBER => $number, Header::LANGUAGE => 'ja'];
        $response = $this->call('GET', '/api/scryfall', $query);
        $response->assertOk();
        $response->assertJsonPath(Con::IMAGE_URL, $this->createImageUrl($url));
    }

    public static function imageProvider(): array
    {
        return [
            '表面のみ' => ['IKO', 1, 'e/1/e1059d5b-1de6-4988-a3a8-fe540a541342.png?1645734158'],
            '上下二面カード' => ['AKH', 210, '4/c/4cffc5c9-0115-4a8f-9665-a3fbdd4179c2.png?1540281378'],
            '表裏両面カード' => ['MH3', 261, '3/0/305ae3b5-7c12-43ad-b19f-dbd04c9afcf7.png?1730229671'],
        ];
    }

    #[Test]
    #[TestDox('存在しないカード情報をリクエストした場合、404エラーが返ることを検証する。')]
    public function nocardinfo()
    {
        $query = [Header::SETCODE => 'ECL', Con::NUMBER => '9999', Header::LANGUAGE => 'ja'];
        $response = $this->call('GET', '/api/scryfall', $query);
        $this->assertApiError($response, 'api/scryfall', '情報なし', '指定した情報がありません。', 404);
    }

    public function ok(string $setcode, string $number, string $lang)
    {
        $query = [Header::SETCODE => $setcode, Con::NUMBER => $number, Header::LANGUAGE => $lang];
        $response = $this->call('GET', '/api/scryfall', $query);
        $response->assertOk();
        return $response;
    }

    /**
     * 期待値の画像URLを作成する。
     *
     * @param string $png
     * @return string 画像URL
     */
    private function createImageUrl(string $png): string
    {
        return 'https://cards.scryfall.io/png/front/' . $png;
    }
}
