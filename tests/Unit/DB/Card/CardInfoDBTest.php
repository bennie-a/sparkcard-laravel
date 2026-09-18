<?php

namespace Tests\Unit\DB\Card;

use App\Facades\ScryfallServ;
use App\Http\Response\CustomResponse;
use App\Models\CardInfo;
use App\Models\Expansion;
use App\Models\Foiltype;
use App\Services\Constant\CardConstant as Con;
use App\Services\Constant\GlobalConstant;
use Illuminate\Http\Response;
use Tests\Database\Seeders\DatabaseSeeder;
use Tests\Database\Seeders\TestCardInfoSeeder;
use Tests\Database\Seeders\TruncateAllTables;
use Tests\TestCase;

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertSame;
use PHPUnit\Framework\Attributes\DataProvider;
use App\Services\Constant\GlobalConstant as GCon;
use GMP;
use PHPUnit\Framework\Attributes\TestWith;

/**
 * カード情報を登録するテストクラス
 */
class CardInfoDBTest extends TestCase
{
    public function setup():void
    {
        parent::setup();
        $this->seed(TruncateAllTables::class);
        $this->seed(DatabaseSeeder::class);
        $this->seed(TestCardInfoSeeder::class);
    }

    #[TestDox('片面カードの画像URLが取得できているか検証する。')]
    #[TestWith(['WAR', 462248, '', 'https://cards.scryfall.io/png/front/d/3/d35f6ec2-7b39-458a-8600-c948c438252a.png?1782922433'], 'multiverseIdあり')]
    #[TestWith(['WAR', 0, '', ''], 'image_urlあり_multiverseIdとscryfallIdなし')]
    #[TestWith(['WAR', 0, '106e75ca-42a2-435c-8446-34763bbed5da',
        'https://cards.scryfall.io/png/front/1/0/106e75ca-42a2-435c-8446-34763bbed5da.png?1782922335'], 'scryfallIdあり')]
    #[TestWith(['NEO', 0, '8ae8fd65-4e02-4033-8712-3d15eee4a09c',
         'https://cards.scryfall.io/png/front/8/a/8ae8fd65-4e02-4033-8712-3d15eee4a09c.png?1782862991'], '両面カード')]
    #[TestWith(['ECL', 0, '19cba6be-7291-4788-9241-87dad3b68363',
         'https://cards.scryfall.io/png/front/1/9/19cba6be-7291-4788-9241-87dad3b68363.png?1783904370'], 'リバーシブル・ボーダーレス')]
    #[TestWith(['DSK', 674764, '', 'https://cards.scryfall.io/png/front/d/3/d34c1354-3a78-4523-9b9a-58bf0c2b1a4e.png?1782785604'],
     '分割カード')]
    // #[TestWith(['FRA', 0, '0853bb80-8664-432a-8457-600139fd96d5',
    //      'https://cards.scryfall.io/png/front/0/8/0853bb80-8664-432a-8457-600139fd96d5.png?1788878145'], '準備カード')]
    public function test_getImage(string $setcode, int $multiId, string $scryId, string $exUrl) {
        $name = fake()->realText(10);
        $params = $this->createParams($setcode, $name, 1, ['通常版']);
        $params[Con::MULTIVERSEID] = $multiId;
        if ($multiId > 0 || $scryId !== '') {
            unset($params[Con::IMAGE_URL]);
        }
        if(!empty($scryId)) {
            $params[Con::SCRYFALLID] = $scryId;
        }
        $this->post_execute($params, Response::HTTP_CREATED);
        $exp = Expansion::findBySetCode($setcode);
        $this->assertDatabaseHas(CardInfo::class, [
            Con::EXP_ID => $exp->notion_id,
            Con::NUMBER => $params[Con::NUMBER],
            Con::FOIL_ID =>   Foiltype::findByAttr(Con::NON_FOIL)->id,
            Con::IMAGE_URL => empty($exUrl) ? $params[Con::IMAGE_URL] : $exUrl
        ]);
    }

    /**
     * 通常版/特別版カードの登録テスト
     *
     * @param string $setcode セット略称
     * @param string $name カード名
     * @param int $promotypeId プロモタイプID
     * @param array $foiltype Foilタイプ
     * @return void
     */
    #[DataProvider('specialcardprovider')]
    public function test_specialcard(string $setcode, string $name,  int $promotypeId, array $foiltype) {
        $params = $this->createParams($setcode, $name, $promotypeId, $foiltype);
        $this->post_execute($params, 201);
        foreach($foiltype as $type) {
            $type = Foiltype::findByName($type);
            assertNotNull($type, 'Foilタイプの存在確認');
            $exp = Expansion::findBySetCode($setcode);
            $this->assertDatabaseHas('card_info', [
                Con::EXP_ID => $exp->notion_id,
                GCon::NAME => $params[GCon::NAME],
                Con::EN_NAME => $params[Con::EN_NAME],
                'color_id' => $params[Con::COLOR],
                Con::NUMBER => $params[Con::NUMBER],
                Con::FOIL_ID => $type->id,
                Con::PROMO_ID => $promotypeId
            ]);
        }
    }

    public static function specialcardprovider() {
        return [
            '通常版' => ['BRO', '出現領域', 1, ['通常版']],
            '特別版_共通' => ['NEO', '発展の暴君、ジン＝ギタクシアス', 2, ['通常版', 'Foil']],
            '特別版_セット特有' => ['MOM', '族樹の精霊、アナフェンザ', 13, ['ハロー・Foil']],
            '箔押し' => ['MOM', 'アート・カード', 1, ['通常版', '箔押し']],
        ];
    }

    private function createParams(string $setcode, string $name, int $promotypeId, array $foiltype): array{
        $data = ['setCode' => $setcode,
                            GCon::NAME => $name,
                            Con::EN_NAME => $this->getEnName(),
                            Con::COLOR => fake()->randomElement(['U', 'B', 'R', 'G', 'W', 'Land']),
                            Con::NUMBER => fake()->numberBetween(1, 400),
                            Con::IMAGE_URL => fake()->url(),
                           Con::PROMO_ID => $promotypeId, Con::FOIL_TYPE => $foiltype, 'isSkip' => false];
        return $data;
    }

    #[TestWith(['Art'], 'アート・カード')]
    #[TestDox('カードの色の登録に関する検証')]
    public function test_color(string $color) {
        $setcode = 'BRO';
        $name = fake()->realText(10);
        $params = $this->createParams($setcode, $name, 1, ['通常版']);
        $params[Con::COLOR] = $color;
        $this->post_execute($params, 201);

        $exp = Expansion::findBySetCode($setcode);
        $this->assertDatabaseHas('card_info', [
            Con::EXP_ID => $exp->notion_id,
            GCon::NAME => $params[GCon::NAME],
            Con::EN_NAME => $params[Con::EN_NAME],
            'color_id' => $params[Con::COLOR],
            Con::NUMBER => $params[Con::NUMBER],
            Con::PROMO_ID => 1
        ]);
    }

    private function post_execute($data, int $statuscode) {
        return $this->post('api/database/card', $data)->assertStatus($statuscode);
    }

    private function getEnName() {
        return fake()->sentence(5);
    }

    /**
     * エラーケース
     * @return void
     */
    #[DataProvider('errorcase')]
    public function test_error(string $setcode, array $foiltype, string $title) {
        $name = fake()->realText(10);
        $params = $this->createParams($setcode, $name, 1, $foiltype);
        $response = $this->post_execute($params, Response::HTTP_NOT_FOUND);
        $response->assertJsonStructure(['title', 'detail', 'status','request']);
        $this->assertEquals($title, $response->json('title'), 'タイトルの確認');
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->json('status'), 'ステータスコード');
    }

    public static function errorcase() {
        return [
            'setCodeがDBになし' => ['XXX', ['通常版'], 'エキスパンションなし'],
            '不明なFoilタイプ' => ['WAR', ['不明'], 'Foilタイプなし'],
        ];
    }
}
