<?php

namespace Tests\Unit\DB\Card;

use App\Facades\ScryfallServ;
use App\Http\Response\CustomResponse;
use App\Models\CardInfo;
use App\Models\Expansion;
use App\Models\Foiltype;
use App\Services\Constant\CardConstant as Con;
use App\Services\Constant\GlobalConstant;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
    public function test_getImage(string $setcode, string $name, string $color, int $number, int $multiId, string $scryId) {
        $enname = $this->getEnName($setcode, $number);
        $data = ['setCode' => $setcode,
                        Con::NAME => $name,
                        Con::EN_NAME => $enname,
                        Con::COLOR => $color,
                        Con::NUMBER => $number,
                        'multiverseId' => $multiId,
                       Con::PROMOTYPE => '', 'scryfallId' => $scryId, Con::FOIL_TYPE => ['通常版', 'Foil'], 'isSkip' => false];
        $this->post_ok($data);
    }

    public static function imageprovider() {
        return[
            'multiverseIdあり' => ['WAR', '出現領域', 'Land', 245, 462492, (string)Str::uuid()],
            'scryfallIdあり' => ['WAR', '出現領域', 'Land', 245, 0, '44f182dc-ae39-447a-9979-afd56bed6794'],
            'multiverseIdとscryfallIdなし' => ['WAR', '出現領域', 'Land', 245, 0, (string)Str::uuid()],
            '両面カード' => ['NEO', '永岩城の修繕', 'W', 442, 551715, (string)Str::uuid()],
            '画像URL更新' => ['NEO', '告別≪ショーケース≫', 'W', 365,552454, (string)Str::uuid()],
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
        $this->post('api/database/card', $data)->assertStatus($statuscode);
    }

    private function getEnName() {
        return fake()->sentence(5);
    }

    /**
     * エラーケース
     * @return void
     */
    #[DataProvider('errorcase')]
    public function test_error(string $setcode, string $name, string $enname, 
                                            int $number, int $multiId, int $statuscode, array $foiltype) {
        $data = ['setCode' => $setcode,
                            Con::NAME => $name,
                            Con::EN_NAME => $enname,
                            Con::COLOR => 'Land',
                            Con::NUMBER => $number,
                            'multiverseId' => $multiId,
                           Con::PROMOTYPE => '', 'scryfallId' => '', Con::FOIL_TYPE => $foiltype, 'isSkip' => false];
        $this->post_execute($data, $statuscode);
    }

    public static function errorcase() {
        return [
            'setCodeがDBになし' => ['XXX', '出現領域', 'Emergence Zone', 245, 462492, 441, ['通常版']],
            '不明なFoilタイプ' => ['WAR', '出現領域', 'Emergence Zone',
                                                     245, 462492, CustomResponse::HTTP_NOT_FOUND_FOIL, ['不明']]
        ];
    }

    public function test_search(string $name, string $set, string $color, bool $isFoil) {

    }
    // public function test_検索() {
    //     $condition = ['name' => '', 'set' => 'BRO', 'color' => 'W', 'isFoil' => false];
    //     $response = $this->json('GET', 'api/database/card', $condition)->assertOk();
    //     $response->assertJsonCount(5);
    //     $json = $response->baseResponse->getContent();
    //     $contents = json_decode($json, true);
    //     foreach($contents as $line) {
    //         assertTrue(array_key_exists('index', $line), 'index');
    //         assertTrue(array_key_exists('name', $line), 'name');
    //         assertTrue(array_key_exists('enname', $line), 'enname');
    //         assertTrue(array_key_exists('image', $line), 'image');
    //         assertTrue(array_key_exists('color', $line), 'color');
    //         assertEquals('白', $line['color'], '色の返り値');
    //     }
    // }

    // public function test_検索_検索結果なし() {
    //     CardInfo::factory()->count(5)->create(['exp_id' => $this->bro->notion_id,
    //      'color_id' => 'W', 'isFoil' => false,  'en_name' =>'aaaaa']);
    //     $condition = ['name' => '', 'set' => $this->bro->attr, 'color' => 'U', 'isFoil' => false];
    //     $response = $this->json('GET', 'api/database/card', $condition)
    //                                 ->assertStatus(Response::HTTP_NO_CONTENT);
    // }
 
}
