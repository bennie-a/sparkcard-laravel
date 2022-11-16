<?php

namespace Tests\Unit\DB;

use App\Models\CardInfo;
use App\Models\Expansion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertIsInt;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertTrue;

class CardInfoDBTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
    }
    
    public function setup():void
    {
        parent::setup();
        $exp = new Expansion();
        $exp->create(['notion_id' => '80a7660b-3de0-4ce1-8e51-1e90e123faae',
        'base_id' => 4261763,
        'name' => '灯争大戦',
        'attr' => 'WAR']);
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
                'promotype' => '', 'scryfallId' => ''];
        $record = $this->post_ok($data);
        assertNotNull($record->image_url, '画像URLの有無');
        assertTrue(str_starts_with($record->image_url, 'https://cards.scryfall.io/normal/front/'), '画像URLの一致');
    }
    
    public function test_登録_scryfallIdあり()
    {
        // 画像URL
    }
    
    public function test_登録_multiverseIdとscryfallIdなし()
    {
        // 画像URL
    }

    public function test_登録_promotypeが絵違い()
    {
        
    }
    
    public function test_登録_promotypeがブースターファン()
    {
        
    }
    
    public function test_登録_promotypeがBOXプロモ特典()
    {
        
    }
    
    private function post_ok($data)
    {
        $this->post('api/database/card', $data)->assertStatus(201);
        $record = CardInfo::first();
        assertNotNull($record, '登録の有無');
        assertEquals($data['name'], $record->name, 'カード名');
        assertEquals($data['en_name'], $record->en_name, 'カード名(英名)');
        assertEquals($data['color'], $record->color_id, '色');
        assertEquals($data['number'], $record->number, 'カード番号');
        assertIsInt(16, strlen($record->barcode), 'バーコード');

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
                'promotype' => ''];
        $response = $this->post('api/database/card', $data)->assertStatus(422);
    }
    
    public function tearDown():void
    {
        CardInfo::where('exp_id', '80a7660b-3de0-4ce1-8e51-1e90e123faae')->delete();
        Expansion::where('name', '灯争大戦')->delete();
    }
    
    public static function tearDownAfterClass(): void
    {
    }
}
