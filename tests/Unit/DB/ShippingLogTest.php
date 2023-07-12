<?php

namespace Tests\Feature\tests\Unit\DB;

use App\Facades\CardBoard;
use App\Models\CardInfo;
use App\Models\Expansion;
use App\Models\ShippingLog;
use App\Models\Stockpile;
use FiveamCode\LaravelNotionApi\Entities\Properties\Text;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ShippingLogTest extends TestCase
{

    public function setup():void {
        parent::setup();
        $this->bro =  Expansion::factory()->createOne(['name' => '兄弟戦争', 'attr' => 'BRO']);
        $draft = ['exp_id' => $this->bro->notion_id, 'name' => 'ファイレクシアのドラゴン・エンジン',
        'en_name' => 'Phyrexian Dragon Engine', 'color_id' => 'A', 'number' => '163',
         'isFoil' => false, 'image_url' => ''];
        $draftinfo = CardInfo::factory()->createOne($draft);
        Stockpile::factory()->createOne(['card_id' => $draftinfo->id, 'condition' => 'NM', 'quantity' => 1, 'language' => 'JP']);

        $foil = ['exp_id' => $this->bro->notion_id, 'name' => 'ファイレクシアのドラゴン・エンジン',
        'en_name' => 'Phyrexian Dragon Engine', 'color_id' => 'A', 'number' => '163',
         'isFoil' => true, 'image_url' => ''];
         $foilinfo = CardInfo::factory()->createOne($foil);
         Stockpile::factory()->createOne(['card_id' => $foilinfo->id, 'condition' => 'NM-', 'quantity' => 3, 'language' => 'JP']);

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
        $this->markTestSkipped('一時的にスキップ');
        $dir = dirname(__FILE__, 4).'\storage\test\sms\\';
        $response = $this->post('/api/shipping/import', ['path' => $dir.'shipping_log.csv']);

        $response->assertStatus(201);
        $response->assertJson(['row' => 4, 'success' => 2, 'skip' => 0, 'error' => 2, 'details' => [2 => '在庫情報なし', 5 => '在庫が0枚です']]);
        logger()->debug($response->json());
    }

    public function test_notion() {
        $page = \CardBoard::findBySpcId(6171);
        logger()->debug($page->getTitle());
        // 状態
        // $selectProp = $page->getProperty('状態');
        // logger()->debug($selectProp->getName());

        $properties = $page-> getProperties();
        foreach($properties as $p){
            $value = '';
            switch($p->getType()) {
                case 'title':
                break;
                case 'formula':
                break;
                case 'relation':
                break;
                case 'rollup':
                    break;
                    case 'url':
                    $value = $p->getUrl();
                break;
                case 'number':
                    $value = round($p->getNumber());
                break;
                case 'checkbox':
                    $value = $p->isChecked();
                break;
                case 'rich_text':
                    $value = $p->getPlainText();
                break;
                case 'last_edited_time':
                    $value = $p->getContent();
                break;
                case 'date':
                    $value = $p->getStart();
                    break;
                default:
                    $value = $p->getName();
            }

            logger()->debug('項目',[$p->getTitle() => $value]);
        }
        // $selectProp->getItem();
    }

    public function tearDown():void
    {
        ShippingLog::query()->delete();
        Stockpile::query()->delete();
        CardInfo::query()->delete();
        Expansion::query()->delete();
    }
}
