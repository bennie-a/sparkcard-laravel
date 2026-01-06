<?php

namespace Tests\Unit\DB\Card;

use App\Facades\ScryfallServ;
use App\Http\Response\CustomResponse;
use App\Models\CardInfo;
use App\Models\Expansion;
use App\Models\Foiltype;
use App\Services\Constant\CardConstant as Con;
use App\Services\Constant\CardConstant;
use App\Services\Constant\GlobalConstant;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\DataProvider;

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

/**
 * 画像URL取得に関する検証テスト
 *
 * @param string $name
 * @param string $color
 * @param integer $number
 * @param integer $multiId
 * @param string $scryId
 * @return void
 */
    #[DataProvider('imageprovider')]
    public function test_getImage(string $setcode, int $multiId, string $scryId) {
        $name = fake()->realText(10);
        $params = $this->createParams($setcode, $name, 1, ['通常版']);
        $params[Con::MULTIVERSEID] = $multiId;
        if ($multiId > 0 && $scryId !== '') {
            unset($params[Con::IMAGE_URL]);
        }
        if(!empty($scryId)) {
            $params[Con::SCRYFALLID] = $scryId;
        }
        $this->post_execute($params, 201);

        $info = CardInfo::getCardinfo(
            Expansion::findBySetCode($setcode)->notion_id,
            $params[Con::NUMBER],
            Foiltype::findByAttr(Con::NON_FOIL)->id
        );
        $this->assertNotNull($info, 'カード情報の取得');
        $this->assertNotNull($info->image_url, '画像URLの取得');
    }

    public static function imageprovider() {
        return[
            'multiverseIdあり' => ['WAR', 462492, ''],
            'scryfallIdあり' => ['WAR', 0, '44f182dc-ae39-447a-9979-afd56bed6794'],
            'image_urlあり_multiverseIdとscryfallIdなし' => ['WAR', 0, ''],
            '両面カード' => ['NEO', 551715, ''],
            'リバーシブル・ボーダーレス' => ['ECL', 0, '19cba6be-7291-4788-9241-87dad3b68363'],
        ];
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
                GlobalConstant::NAME => $params[GlobalConstant::NAME],
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
            '特別版_セット特有' => ['MOM', '族樹の精霊、アナフェンザ', 13, ['ハロー・Foil']]
        ];
    }

    private function createParams(string $setcode, string $name, int $promotypeId, array $foiltype): array{
        $data = ['setCode' => $setcode,
                            Con::NAME => $name,
                            Con::EN_NAME => $this->getEnName(),
                            Con::COLOR => fake()->randomElement(['U', 'B', 'R', 'G', 'W', 'Land']),
                            Con::NUMBER => fake()->numberBetween(1, 400),
                            Con::IMAGE_URL => fake()->url(),
                           Con::PROMO_ID => $promotypeId, Con::FOIL_TYPE => $foiltype, 'isSkip' => false];
        return $data;
    }

    private function post_ok($data)
    {
        // $this->markTestSkipped('一時スキップ');
        $this->post_execute($data, 201);

        $setcode = $data['setCode'];
        $exp = Expansion::where('attr', '=', $setcode)->first();
        $number = $data[Con::NUMBER];
        $record = CardInfo::where(['exp_id' => $exp->notion_id, 'number' => $number])->get();
        $foiltype =  $data[Con::FOIL_TYPE];
        assertCount(count($foiltype), $record, '登録レコードの有無');

        foreach($foiltype as $foil) {
            $type = Foiltype::findByName($foil);
            assertNotNull($type);
            $actual = $record->first(function($item) use ($type) {
                return $item->foiltype_id == $type->id;
            });
            assertNotNull($actual, $foil);

            $name =  $data[Con::NAME];
            $name = empty($data[Con::PROMOTYPE]) ? $name : $name.'≪'.$data[Con::PROMOTYPE].'≫';
            assertEquals($name, $actual->name, 'カード名');
            assertEquals($data[Con::EN_NAME], $actual->en_name, 'カード名(英名)');
            assertEquals($data[Con::COLOR], $actual->color_id, '色');
            assertEquals($data[Con::NUMBER], $actual->number, 'カード番号');
            $isFoil = $foil != '通常版';
            assertSame($isFoil, $actual->isFoil, 'Foilフラグ');
            if ($data['multiverseId'] != 0 || !empty($data['scryfallId'])) {
                assertNotNull($actual->image_url, '画像URL');
            } else {
                assertNull($actual->image_url, '画像URL');
            }
        }
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
