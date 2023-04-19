<?php

namespace Tests\Unit\DB;

use App\Models\Expansion;
use App\Repositories\Api\Notion\ExpansionRepository;
use App\Services\ScryfallService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotNull;
use function Ramsey\Uuid\Lazy\toString;

class DBExpTest extends TestCase
{
    public function setup():void
    {
        parent::setUp();
        DB::table('expansion')->truncate();
         Expansion::factory()->createOne(['name' => '灯争大戦', 'attr' => 'WAR']);
    }

    /**
     * Undocumented function
    *
    * @param array $storeData
    * @return void
    *  @dataProvider dataprovider
    */
    public function testExecute(array $storeData, int $code, string $expected) {
        $response = $this->post('api/database/exp', $storeData);
        $response->assertStatus($code);

        if ($code == Response::HTTP_CREATED) {
        // DBからデータ取得
        $dbactual = Expansion::where('attr', $storeData['attr'])->first();
        assertNotNull($dbactual, 'DB登録の有無');
        assertEquals($dbactual->name, $storeData['name'], '名称');
        assertEquals($dbactual->attr, $storeData['attr'], '略称');
        // リリース日
        $date = $storeData['release_date'];
        if (!is_null($date)) {
            $service = new ScryfallService();
            $date = $service->getReleaseDate($storeData['attr']);
        }
        assertEquals($date, $dbactual->release_date, 'リリース日');

        $repo = new ExpansionRepository();
        $actualPage = $repo->findById($dbactual->notion_id);
        assertNotNull($actualPage, 'Notion登録の有無');
        $array = $actualPage->toArray();
        assertEquals($storeData['name'], $array['title'] , '名称');
        $attr = $actualPage->getProperty('略称')->getRawContent()[0]['plain_text'];
        assertEquals($storeData['attr'], $attr,  '略称');
        assertEquals($storeData['block'], $actualPage->getProperty('ブロック')->getRawContent()['name'],  'ブロック');
        assertEquals($storeData['format'], $actualPage->getProperty('フォーマット')->getRawContent()['name'],  'フォーマット');
        } else {
            $json = $response->json();
            assertEquals($expected, $json['detail'], 'エラーメッセージ');
        }
    }

    public function dataprovider() {
        return [
            'リリース日あり' => [
                    [
                        'name' => 'コールドスナップ',
                        'attr'=>'CSP',
                        'block'=>'アイスエイジ',
                        'format'=>'モダン',
                        'release_date'=>'2006-07-21'
                    ],
                    Response::HTTP_CREATED,
                    ''
                ],
                'リリース日なし' => [
                    [
                        'name' => '兄弟戦争',
                        'attr'=>'BRO',
                        'block'=>'ドミナリア',
                        'format'=>'スタンダード',
                        'release_date'=>'',
                    ],
                    Response::HTTP_CREATED,
                    ''
                ],
            'エキスパンションが重複する' => [
                    [
                        'name' => '灯争大戦',
                        'attr'=>'WAR',
                        'block'=>'ラヴニカ',
                        'format'=>'パイオニア',
                        'release_date'=>'2011-07-21'
                    ],
                    Response::HTTP_CONFLICT,
                    '灯争大戦は既に登録されています。'
            ],
        ];
    }
}
