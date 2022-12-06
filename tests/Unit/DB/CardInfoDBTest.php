<?php

namespace Tests\Unit\DB;

use App\Models\CardInfo;
use App\Models\Expansion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertIsInt;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertTrue;

class CardInfoDBTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
    }
    
    public function setup():void
    {
        parent::setup();
        $this->war = Expansion::factory()->createOne(['name' => '灯争大戦','attr' => 'WAR']);
        $this->bro = Expansion::factory()->createOne(['name' => '兄弟戦争', 'attr' => 'BRO']);
        $this->dmu = Expansion::factory()->createOne(['name' => '団結のドミナリア', 'attr' => 'DMU']);
    }
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_登録_multiverseIdあり()
    {
        $data = ['setCode' => 'WAR',
                'name' => '出現領域',
                'en_name' => 'Emergence Zone',
                'color' => 'Land',
                'number'=> '245',
                'multiverseId' => '462492',
                'promotype' => '', 'scryfallId' => '', 'isFoil' => false];
        $record = $this->post_ok($data);
        assertEquals($data['name'], $record->name, 'カード名');
        assertNotNull($record->image_url, '画像URLの有無');
        assertEquals($record->image_url, 'https://cards.scryfall.io/png/front/4/4/44f182dc-ae39-447a-9979-afd56bed6794.png?1646071757', '画像URLの一致');
        assertFalse($record->isFoil, '通常版/Foil');
    }
    
    public function test_登録_scryfallIdあり()
    {
        $data = ['setCode' => 'WAR',
                'name' => '群れの声、アーリン',
                'en_name' => 'Arlinn, Voice of the Pack',
                'color' => 'G',
                'number'=> '150',
                'multiverseId' => '',
                'promotype' => '絵違い', 
                'scryfallId' => '43261927-7655-474b-ac61-dfef9e63f428','isFoil' => false];
        $record = $this->post_ok($data);
        assertEquals($data['name'].'≪'.$data['promotype'].'≫', $record->name, 'カード名');
        assertNotNull($record->image_url, '画像URLの有無');
        assertFalse($record->isFoil, '通常版/Foil');
    }
    
    public function test_登録_multiverseIdとscryfallIdなし()
    {
        $data = ['setCode' => 'WAR',
                'name' => '飛空士の騎兵部隊',
                'en_name' => 'Aeronaut Cavalry',
                'color' => 'W',
                'number'=> '1',
                'multiverseId' => '',
                'promotype' => '', 
                'scryfallId' => '', 'isFoil' => false];
        $record = $this->post_ok($data);
        assertEquals($data['name'], $record->name, 'カード名');
        assertNull($record->image_url, '画像URLの有無');
        assertFalse($record->isFoil, '通常版/Foil');
    }
    
    
    public function test_登録_Foil版()
    {
        $data = ['setCode' => 'WAR',
                'name' => '群れの声、アーリン',
                'en_name' => 'Arlinn, Voice of the Pack',
                'color' => 'G',
                'number'=> '150',
                'multiverseId' => '',
                'promotype' => '絵違い', 
                'scryfallId' => '43261927-7655-474b-ac61-dfef9e63f428','isFoil' => true];
        $record = $this->post_ok($data);
        assertEquals($data['name'].'≪'.$data['promotype'].'≫', $record->name, 'カード名');
        assertNotNull($record->image_url, '画像URLの有無');
        assertTrue($record->isFoil, '通常版/Foil');
    }


    private function post_ok($data)
    {
        $this->post('api/database/card', $data)->assertStatus(201);
        $list = CardInfo::where(['en_name' => $data['en_name'], 'isFoil' => $data['isFoil']])->get();
        assertTrue($list->count() == 1, '登録の有無');
        $record = $list[0];
        assertEquals($data['en_name'], $record->en_name, 'カード名(英名)');
        assertEquals($data['color'], $record->color_id, '色');
        assertEquals($data['number'], $record->number, 'カード番号');
        assertIsInt(16, strlen($record->barcode), 'バーコード');

        $exp = Expansion::where('attr', $data['setCode'])->first();
        assertEquals($this->war->notion_id, $record->exp_id, 'エキスパンションID');
        return $record;
    }

    public function test_setCodeがDBになし() 
    {
        $data = ['setCode' => 'XXX',
                'name' => '出現領域',
                    'en_name' => 'Emergence Zone',
                    'color' => 'Land',
                'number'=> '245',
                'multiverseId' => '462492',
                'scryfallId' => '',
                'promotype' => '', 'isFoil' => false];
        $this->post('api/database/card', $data)->assertStatus(422);
    }

    public function test_検索() {
        CardInfo::factory()->count(5)->create(['exp_id' => $this->bro->notion_id, 'color_id' => 'W']);
        CardInfo::factory()->count(5)->create(['exp_id' => $this->bro->notion_id, 'color_id' => 'U']);

        $condition = ['set' => $this->bro->attr, 'color' => 'W'];
        $response = $this->json('GET', 'api/database/card', $condition)->assertOk();
        $response->assertJsonCount(5);
        $json = $response->baseResponse->getContent();
        $contents = json_decode($json, true);
        foreach($contents as $line) {
            assertTrue(array_key_exists('index', $line), 'index');
            assertTrue(array_key_exists('name', $line), 'name');
            assertTrue(array_key_exists('enname', $line), 'enname');
            assertTrue(array_key_exists('image', $line), 'image');
            assertTrue(array_key_exists('color', $line), 'color');
            assertEquals('白', $line['color'], '色の返り値');
        }
    }

    public function test_検索_検索結果なし() {
        CardInfo::factory()->count(5)->create(['exp_id' => $this->bro->notion_id, 'color_id' => 'W']);
        $condition = ['set' => $this->bro->attr, 'color' => 'U'];
        $response = $this->json('GET', 'api/database/card', $condition)
                                    ->assertStatus(Response::HTTP_NO_CONTENT);
    }
    
    public function tearDown():void
    {
        CardInfo::query()->delete();
        Expansion::query()->delete();
    }
}
