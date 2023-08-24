<?php

namespace Tests\Unit\DB;

use App\Facades\ExService;
use App\Facades\ScryfallServ;
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
    }

//     public function test_両面カード() {
//                 $data = ['setCode' => 'NEO',
//                 'name' => '永岩城の修繕',
//                 'en_name' => 'The Restoration of Eiganjo // Architect of Restoration',
//                 'color' => 'W',
//                 'number'=> '442',
//                 'multiverseId' => '551715',
//                 'promotype' => '', 
//                 'scryfallId' => '','isFoil' => false,
//             'language' => 'JP'];
//         $record = $this->post_execute($this->neo, $data);
//         assertEquals("https://cards.scryfall.io/png/front/0/7/070d6344-ee01-4e27-a513-467d8775a853.png?1657724945",
//                      $record['image_url'], "画像");
//     }

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
public function test_getImage(string $name, string $color, int $number, int $multiId, string $scryId) {
    $setcode = 'WAR';
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
            'multiverseIdあり' => ['出現領域', 'Land', 245, 462492, ''],
            'scryfallIdあり' => ['群れの声、アーリン', 'G', 150, 0, '43261927-7655-474b-ac61-dfef9e63f428'],
            'multiverseIdとscryfallIdなし' => ['出現領域', 'Land', 245, 0, '']
        ];
    }

    /**
     * プロモタイプ登録テスト
     *@dataProvider promoprovider
     * @return void
     */
    public function test_promotype(int $number, int $multiId, string $promotype) {
        $setcode = 'NEO';
        $enname = $this->getEnName($setcode, $number);
        $data = ['setCode' => $setcode,
                            Con::NAME => '発展の暴君、ジン＝ギタクシアス',
                            Con::EN_NAME => $enname,
                            Con::COLOR => 'U',
                            Con::NUMBER => $number,
                            'multiverseId' => $multiId,
                           Con::PROMOTYPE => $promotype, 'scryfallId' => '', Con::FOIL_TYPE => ['通常版', 'Foil'], 'isSkip' => false];
            $this->post_ok($data);
    
    }
    public function promoprovider() {
        return [
            '特別版' => [371, 552460, 'ショーケース']
            // '特別版' => []
        ];
    }

    private function post_ok($data)
    {
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
        $api = ScryfallServ::getCardInfoByNumber(["setcode" => $setcode, Con::NUMBER => $number, 'language' => "ja"]);
        return $api[Con::EN_NAME];
    }

//     public function test_setCodeがDBになし() 
//     {
//         $data = ['setCode' => 'XXX',
//                 'name' => '出現領域',
//                     'en_name' => 'Emergence Zone',
//                     'color' => 'Land',
//                 'number'=> '245',
//                 'multiverseId' => '462492',
//                 'scryfallId' => '',
//                 'promotype' => '', 'isFoil' => false,
//             'language' => 'JP'];
//         $response = $this->post('api/database/card', $data)->assertStatus(441);
//     }

//     public function test_検索() {
//         CardInfo::factory()->count(5)->create(['exp_id' => $this->bro->notion_id,
//                                                  'color_id' => 'W', 'isFoil' => false, 'en_name' =>'aaaaa' ]);
//         CardInfo::factory()->count(5)->create(['exp_id' => $this->bro->notion_id, 
//                                                     'color_id' => 'U', 'isFoil' => false,  'en_name' =>'bbbbb']);

//         $condition = ['name' => '', 'set' => $this->bro->attr, 'color' => 'W', 'isFoil' => false];
//         $response = $this->json('GET', 'api/database/card', $condition)->assertOk();
//         $response->assertJsonCount(5);
//         $json = $response->baseResponse->getContent();
//         $contents = json_decode($json, true);
//         foreach($contents as $line) {
//             assertTrue(array_key_exists('index', $line), 'index');
//             assertTrue(array_key_exists('name', $line), 'name');
//             assertTrue(array_key_exists('enname', $line), 'enname');
//             assertTrue(array_key_exists('image', $line), 'image');
//             assertTrue(array_key_exists('color', $line), 'color');
//             assertEquals('白', $line['color'], '色の返り値');
//         }
//     }

//     public function test_検索_検索結果なし() {
//         CardInfo::factory()->count(5)->create(['exp_id' => $this->bro->notion_id,
//          'color_id' => 'W', 'isFoil' => false,  'en_name' =>'aaaaa']);
//         $condition = ['name' => '', 'set' => $this->bro->attr, 'color' => 'U', 'isFoil' => false];
//         $response = $this->json('GET', 'api/database/card', $condition)
//                                     ->assertStatus(Response::HTTP_NO_CONTENT);
//     }
 
}
