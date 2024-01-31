<?php

namespace Tests\Feature\tests\Unit\DB;

use App\Facades\CardBoard;
use App\Models\CardInfo;
use App\Models\Expansion;
use App\Models\ShippingLog;
use App\Models\Stockpile;
use App\Services\Constant\StockpileHeader as Header;;
use FiveamCode\LaravelNotionApi\Entities\Properties\Text;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ShippingLogTest extends TestCase
{
    private $bro;
    public function setup():void {
        parent::setup();
        $this->bro =  Expansion::factory()->createOne(['name' => '兄弟戦争', 'attr' => 'BRO']);
        $draft = ['exp_id' => $this->bro->notion_id, 'name' => 'ファイレクシアのドラゴン・エンジン',
        'en_name' => 'Phyrexian Dragon Engine', 'color_id' => 'A', 'number' => '163',
         'isFoil' => false, 'image_url' => ''];
        $draftinfo = CardInfo::factory()->createOne($draft);
        Stockpile::factory()->createOne(['card_id' => $draftinfo->id, 'condition' => 'NM', 'quantity' => 1, 'language' => 'JP']);
        CardBoard::update('64690625-60e1-4442-bd7b-ddd12bb26d67', ['status' =>'両方販売中']);

        $foil = ['exp_id' => $this->bro->notion_id, 'name' => 'ファイレクシアのドラゴン・エンジン',
        'en_name' => 'Phyrexian Dragon Engine', 'color_id' => 'A', 'number' => '163',
         'isFoil' => true, 'image_url' => ''];
         $foilinfo = CardInfo::factory()->createOne($foil);
         Stockpile::factory()->createOne(['card_id' => $foilinfo->id, 'condition' => 'NM-', 'quantity' => 3, 'language' => 'JP']);
         CardBoard::update('b4c3cc34-ca79-4109-b26d-068e3975fd2f', ['status' =>'両方販売中']);

         $stockzero = ['exp_id' => $this->bro->notion_id, 'name' => 'ドラゴンの運命',
         'en_name' => 'Draconic Destiny', 'color_id' => 'R', 'number' => '130',
          'isFoil' => false, 'image_url' => ''];
          $zeroinfo = CardInfo::factory()->createOne($stockzero);
          Stockpile::factory()->createOne(['card_id' => $zeroinfo->id, 'condition' => 'NM-', 'quantity' => 0, 'language' => 'JP']);
     }
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_import()
    {
        $dir = dirname(__FILE__, 4).'\storage\test\sms\\';
        $response = $this->post('/api/shipping/import', ['path' => $dir.'shipping_log.csv']);

        $response->assertStatus(201);
        $response->assertJson(['row' => 4, 'success' => 2, 'skip' => 0, 'error' => 2, 'details' => [2 => '在庫情報なし', 5 => '在庫が0枚です']]);
        logger()->debug($response->json());
    }

    public function tearDown():void
    {
        ShippingLog::query()->delete();
        Stockpile::query()->delete();
        CardInfo::query()->delete();
        Expansion::query()->delete();
    }
}
