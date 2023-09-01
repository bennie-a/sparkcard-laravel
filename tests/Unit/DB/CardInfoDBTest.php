<?php

namespace Tests\Unit\DB;

use App\Facades\ExService;
use App\Facades\ScryfallServ;
use App\Http\Response\CustomResponse;
use App\Models\CardInfo;
use App\Models\Expansion;
use App\Models\Foiltype;
use App\Services\Constant\JsonFileConstant as Con;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertIsInt;
use function PHPUnit\Framework\assertNotEmpty;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNotSame;
use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

class CardInfoDBTest extends TestCase
{
    use RefreshDatabase;
    public function setup():void
    {
        parent::setup();
        $this->seed('DatabaseSeeder');
        $this->seed('TestExpansionSeeder');
        $this->seed('TestCardInfoSeeder');
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
 * @dataProvider imageprovider
 */
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

    public function imageprovider() {
        return[
            'multiverseIdあり' => ['WAR', '出現領域', 'Land', 245, 462492, ''],
            'scryfallIdあり' => ['WAR', '出現領域', 'Land', 245, 0, '44f182dc-ae39-447a-9979-afd56bed6794'],
            'multiverseIdとscryfallIdなし' => ['WAR', '出現領域', 'Land', 245, 0, ''],
            '両面カード' => ['NEO', '永岩城の修繕', 'W', 442, 551715, ''],
            '画像URL更新' => ['NEO', '告別≪ショーケース≫', 'W', 365,552454, ''],
        ];
    }

    /**
     * 特別版の登録テスト
     *@dataProvider specialcardprovider
     * @return void
     */
    public function test_specialcard(string $setcode, string $name, int $number, int $multiId, string $promotype, string $scryId, array $foiltype) {
        $enname = $this->getEnName($setcode, $number);
        $data = ['setCode' => $setcode,
                            Con::NAME => $name,
                            Con::EN_NAME => $enname,
                            Con::COLOR => 'U',
                            Con::NUMBER => $number,
                            'multiverseId' => $multiId,
                           Con::PROMOTYPE => $promotype, 'scryfallId' => $scryId, Con::FOIL_TYPE => $foiltype, 'isSkip' => false];
            $this->post_ok($data);
    
    }
    public function specialcardprovider() {
        return [
            '特別版' => ['NEO', '発展の暴君、ジン＝ギタクシアス', 371, 552460, 'ショーケース', '', ['通常版', 'Foil']],
            'ハロー・Foil' => ['MUL', '族樹の精霊、アナフェンザ', 131, 0, '',  "262ebc87-fcf5-4893-8357-dcb985a9ba60", ['ハロー・Foil']]
        ];
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

    private function getEnName(string $setcode, int $number) {
        $api = ScryfallServ::getCardInfoByNumber(["setcode" => $setcode, Con::NUMBER => $number, 'language' => "en"]);
        return $api[Con::EN_NAME];
    }

    /**
     * エラーケース
     *@dataProvider errorcase
     * @return void
     */
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

    public function errorcase() {
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
