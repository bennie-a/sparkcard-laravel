<?php

namespace Tests\Unit\DB;

use App\Models\CardInfo;
use App\Models\Expansion;
use Illuminate\Http\Response;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;
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
        $this->neo = Expansion::factory()->createOne(['name' => '神河：輝ける世界', 'attr' => 'NEO']);
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
                'multiverseId' => 462492,
                'promotype' => '', 'scryfallId' => '', 'isFoil' => false];
        $record = $this->post_ok($data);
        assertEquals($data['name'], $record->name, 'カード名');
        assertNotNull($record->image_url, '画像URLの有無');
        assertEquals($record->image_url, 'https://cards.scryfall.io/png/front/4/4/44f182dc-ae39-447a-9979-afd56bed6794.png?1646071757', '画像URLの一致');
    }
    
    public function test_登録_scryfallIdあり()
    {
        $data = ['setCode' => 'WAR',
                'name' => '群れの声、アーリン',
                'en_name' => 'Arlinn, Voice of the Pack',
                'color' => 'G',
                'number'=> '150',
                'multiverseId' => 0,
                'promotype' => '絵違い', 
                'scryfallId' => '43261927-7655-474b-ac61-dfef9e63f428','isFoil' => false, 'isSkip' => false];
        $record = $this->post_ok($data);
        assertEquals($data['name'].'≪'.$data['promotype'].'≫', $record->name, 'カード名');
        assertNotNull($record->image_url, '画像URLの有無');
    }
    
    public function test_登録_multiverseIdとscryfallIdなし()
    {
        $data = ['setCode' => 'WAR',
                'name' => '飛空士の騎兵部隊',
                'en_name' => 'Aeronaut Cavalry',
                'color' => 'W',
                'number'=> '1',
                'multiverseId' => 0,
                'promotype' => '', 
                'scryfallId' => '', 'isFoil' => false];
        $record = $this->post_ok($data);
        assertEquals($data['name'], $record->name, 'カード名');
        assertNull($record->image_url, '画像URLの有無');
    }
    
    
    public function test_登録_Foil版()
    {
        $data = ['setCode' => 'WAR',
                'name' => '群れの声、アーリン',
                'en_name' => 'Arlinn, Voice of the Pack',
                'color' => 'G',
                'number'=> '150',
                'multiverseId' => 0,
                'promotype' => '絵違い', 
                'scryfallId' => '43261927-7655-474b-ac61-dfef9e63f428','isFoil' => true,
            'language' => 'JP', 'isSkip' => false];

        $record = $this->post_ok($data);
        assertEquals($data['name'].'≪'.$data['promotype'].'≫', $record->name, 'カード名');
        assertNotNull($record->image_url, '画像URLの有無');
    }

    public function test_更新_Foil版_同名通常版あり() {
        $exist = ['exp_id' => $this->war->notion_id, 'name' => '群れの声、アーリン≪絵違い≫',
          'en_name' => 'Arlinn, Voice of the Pack', 'color_id' => 'G', 'number' => '150★',
           'isFoil' => true, 'image_url' => ''];
        $duplicate = CardInfo::factory()->createOne($exist);
        $data = ['setCode' => 'WAR',
                'name' => '群れの声、アーリン',
                'en_name' => 'Arlinn, Voice of the Pack',
                'color' => 'G',
                'number'=> '150★',
                'multiverseId' => 0,
                'promotype' => '絵違い', 
                'scryfallId' => '43261927-7655-474b-ac61-dfef9e63f428',
                'isFoil' => true,
                'isSkip' => false];
        $record = $this->post_ok($data);
        assertEquals($duplicate->id, $record->id, 'ID');
        assertEquals($data['name'].'≪'.$data['promotype'].'≫', $record->name, 'カード名');
        assertNotNull($record->image_url, '画像URLの有無');
    }

    public function test_両面カード() {
                $data = ['setCode' => 'NEO',
                'name' => '永岩城の修繕',
                'en_name' => 'The Restoration of Eiganjo // Architect of Restoration',
                'color' => 'W',
                'number'=> '442',
                'multiverseId' => '551715',
                'promotype' => '', 
                'scryfallId' => '','isFoil' => false,
            'language' => 'JP'];
        $record = $this->post_execute($this->neo, $data);
        assertEquals("https://cards.scryfall.io/png/front/0/7/070d6344-ee01-4e27-a513-467d8775a853.png?1657724945",
                     $record['image_url'], "画像");
    }

    private function post_ok($data)
    {
        return $this->post_execute($this->war, $data);
    }

    private function post_execute($exp, $data) {
        $this->post('api/database/card', $data)->assertStatus(201);
        $list = CardInfo::where(['exp_id' => $exp->notion_id, 'number' => $data['number'], 'isFoil' => $data['isFoil']])->get();
        assertTrue($list->count() == 1, '登録の有無');
        $record = $list[0];
        assertEquals($data['en_name'], $record->en_name, 'カード名(英名)');
        assertEquals($data['color'], $record->color_id, '色');
        assertEquals($data['number'], $record->number, 'カード番号');
        assertEquals($data['isFoil'], $record->isFoil, '通常版/Foil');

        $exp = Expansion::where('attr', $data['setCode'])->first();
        assertEquals($exp->notion_id, $record->exp_id, 'エキスパンションID');
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
                'promotype' => '', 'isFoil' => false,
            'language' => 'JP'];
        $response = $this->post('api/database/card', $data)->assertStatus(441);
    }

    public function test_検索() {
        CardInfo::factory()->count(5)->create(['exp_id' => $this->bro->notion_id,
                                                 'color_id' => 'W', 'isFoil' => false, 'en_name' =>'aaaaa' ]);
        CardInfo::factory()->count(5)->create(['exp_id' => $this->bro->notion_id, 
                                                    'color_id' => 'U', 'isFoil' => false,  'en_name' =>'bbbbb']);

        $condition = ['name' => '', 'set' => $this->bro->attr, 'color' => 'W', 'isFoil' => false];
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
        CardInfo::factory()->count(5)->create(['exp_id' => $this->bro->notion_id,
         'color_id' => 'W', 'isFoil' => false,  'en_name' =>'aaaaa']);
        $condition = ['name' => '', 'set' => $this->bro->attr, 'color' => 'U', 'isFoil' => false];
        $response = $this->json('GET', 'api/database/card', $condition)
                                    ->assertStatus(Response::HTTP_NO_CONTENT);
    }
    
    public function tearDown():void
    {
        CardInfo::query()->delete();
        Expansion::query()->delete();
    }
}
