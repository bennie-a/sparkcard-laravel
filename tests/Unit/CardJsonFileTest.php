<?php

namespace Tests\Feature\Unit;

use App\Models\Expansion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Response;
use Tests\TestCase;

use function PHPUnit\Framework\assertEmpty;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotEmpty;
use function PHPUnit\Framework\assertNotNull;

/**
 * カード情報ファイルアップロードのテスト
 */
class CardJsonFileTest extends TestCase
{
    const NAME = 'name';

    const PROMOTYPE = 'promotype';

    const MULTIVERSEID = 'multiverseId';

    const SCRYFALLID = 'scryfallId';

    use RefreshDatabase;
    public function setup():void
    {
        parent::setup();
        $this->seed('DatabaseSeeder');
        $this->seed('TestExpansionSeeder');

    }

    /**
     * カードの言語別テスト
     *
     * @dataProvider dataprovider
     */
    public function test_通常版(string $filename, array $expected)
    {
        // $this->markTestSkipped('一時スキップ');
        $result = $this->execute($filename);
        $exMultiverseId = $expected[self::MULTIVERSEID];
        $exScryId = $expected[self::SCRYFALLID];
        $filterd = array_filter($result, function($a) use($exMultiverseId, $exScryId){
            if (!empty($exMultiverseId)) {
                if ($a[self::MULTIVERSEID] == $exMultiverseId) {
                    return $a;
                }   
            } else if ($a[self::SCRYFALLID] == $exScryId) {
                return $a;
            }
        });
        $actualcard = current($filterd);
        assertNotEmpty($actualcard, '該当カードの有無');
        assertEquals($expected[self::NAME], $actualcard[self::NAME], 'カード名');
        assertEquals($expected[self::MULTIVERSEID], $actualcard[self::MULTIVERSEID], 'multiverseId');
        assertEquals($expected[self::SCRYFALLID], $actualcard[self::SCRYFALLID], 'scryfallId');
        assertEquals($expected['language'], $actualcard['language'], '言語');
    }   

    /**
     * カードタイプのテスト
     * @dataProvider cardtypeProvider
     */
    public function test_カードタイプ(string $filename, array $expected) {
        // $this->markTestSkipped('一時スキップ');

        $result = $this->execute($filename);
        $actualcard = $this->findCard($result, $expected[self::MULTIVERSEID], '');
        assertNotEmpty($actualcard, '結果の有無');
        assertEquals($expected[self::NAME], $actualcard[self::NAME], 'カード名');
    }

    /**
     * 特別版の特定に関するテスト
     * @dataProvider specialdataprovider
     */
    public function test_promotype(string $filename, array $expected) {
        // $this->markTestSkipped('一時スキップ');
        $result = $this->execute($filename);
        $filterd = array_filter($result, function($a) use($expected){
            if ($a[self::NAME] == $expected[self::NAME] && $a[self::PROMOTYPE] == $expected[self::PROMOTYPE]) {
                return $a;
            }
        });
        $actualcard = current($filterd);
        assertNotEmpty($actualcard, '該当カードの有無');
    }

    /**
     * Undocumented function
     *
     * @param string $filename
     * @param integer $expectedCode
     * @param string $expectedMsg
     * @dataProvider errorprovider
     */
    public function test_error(string $filename, int $expectedCode, string $expectedMsg) {

        $header = [
            'headers' => [
                "Content-Type" => "application/json",
            ]
        ];
        $json = $this->json_decode($filename);
        $cards = $json['data']['cards'];
        $data = [
            'data' => [
                'cards' => $cards,
                'code' => $json['data']['code']
            ]
        ];
        $response = $this->post('/api/upload/card', $data, $header);
        $response->assertStatus($expectedCode);
    }

    /**
     * mutiverseIdかscryfallIdに該当するカード情報を取得する。
     *
     * @param array $result
     * @param string $multiId
     * @param string $scryId
     * @return array
     */
    private function findCard($result, string $multiId, string $scryId) {
        $filterd = array_filter($result, function($a) use($multiId, $scryId){
            if (!empty($multiId)) {
                if ($a[self::MULTIVERSEID] == $multiId) {
                    return $a;
                }   
            } else if ($a[self::SCRYFALLID] == $scryId) {
                return $a;
            }
        });
        $actualcard = current($filterd);
        return $actualcard;

    }
    private function execute(string $filename) {
        $header = [
            'headers' => [
                "Content-Type" => "application/json",
            ]
        ];
        $json = $this->json_decode($filename);
        $cards = $json['data']['cards'];
        $data = [
            'data' => [
                'cards' => $cards,
                'code' => $json['data']['code']
            ]
        ];
        $response = $this->post('/api/upload/card', $data, $header);
        $response->assertStatus(201);
        assertEquals($data['data']['code'], $response->json('setCode'), 'Ex略称');

        $result = $response->json('cards');
        return $result;
    }

    /**
     * ファイルパスからjsonファイルを取得する。
     *
     * @param string $path
     * @return array $json
     */
    private function json_decode(string $path) {
        $contents = file_get_contents(storage_path("test/json/".$path));
        $json = json_decode($contents, true);
        return $json;
    }

    /**
     * テストデータ(通常版)
     *
     * @return array 各テストの入力値
     */
    public function dataprovider() {
        return [
            '日本語表記あり' =>['war_short.json', ['name' => 'ジェイスの投影', 'multiverseId' => '463894', 'scryfallId' => '', 'language' => 'JP']],
            '日本語表記あり_multiverseIdなし' => ['mir.json', ['name' => '死後の生命', 'multiverseId' => '3476', 'scryfallId' => '', 'language' => 'JP']],
            '日本語表記なし' =>['test_color.json', ['name' => '飛空士の騎兵部隊', 'multiverseId' => '', 'scryfallId' => '38a62bb2-bc33-44d4-9a7e-92c9ea7d3c2c', 'language' => 'JP']],
            '両面カード' => ['mom.json' ,['name' => 'ラヴニカへの侵攻', 'multiverseId' => '',  'scryfallId' => '73f8fc4f-2f36-4932-8d04-3c2651c116dc',  'language' => 'JP']],
            'ファイレクシア語' => ['neo.json', ['name' => '発展の暴君、ジン＝ギタクシアス', 'multiverseId' => '',  'scryfallId' => 'ffa7cbf8-64b2-428e-8991-d8454d724f9f', 'language' => 'PH']]
        ];
    }

    /**
     * カードタイプ別のテストデータ
     *
     * @return void
     */
    public function cardtypeProvider() {
        return [
            '基本土地' => ['land.json', ['name' => '島(263)', self::MULTIVERSEID => '604650']],
            '特殊土地' => ['land.json', ['name' => 'やせた原野', self::MULTIVERSEID => '465455']],
            'フルアート版土地' => ['land.json', ['name' => '平地(262)', self::MULTIVERSEID => '604649']],
            '冠雪土地' => ['land.json', ['name' => '冠雪の島', self::MULTIVERSEID => '465470']]
        ];
    }

    /**
     * プロモタイプ別のテスト
     *
     * @return array 各テストの入力値
     */
    public function specialdataprovider() {
        return [
            '通常版' => ['war_short.json', [self::NAME => '鮮血の刃先', self::PROMOTYPE => '']],
            '日本限定カード' => ['war_short.json', [self::NAME => '群れの声、アーリン', self::PROMOTYPE => '絵違い']],
            '拡張カード' => ['neo.json', [self::NAME => '発展の暴君、ジン＝ギタクシアス', self::PROMOTYPE => '拡張アート']],
            'ブースターファン' => ['neo.json', [self::NAME => '夜明けの空、猗旺', self::PROMOTYPE => 'ブースターファン']],
            'ショーケース' => ['neo.json', [self::NAME =>  '発展の暴君、ジン＝ギタクシアス', self::PROMOTYPE => 'ショーケース']],
            'フルアート' =>  ['neo.json', [self::NAME =>  '平地(293)', self::PROMOTYPE => 'フルアート']],
            'ネオンインク' => ['neo.json', [self::NAME =>  '貪る混沌、碑出告', self::PROMOTYPE => 'ネオンインク']],
            'ボーダレス「胆液」ショーケース' => ['one.json', [self::NAME =>  '機械の母、エリシュ・ノーン', self::PROMOTYPE => 'ボーダレス「胆液」ショーケース']],
            'コンセプトアート' => ['one.json', [self::NAME =>  '機械の母、エリシュ・ノーン', self::PROMOTYPE => 'コンセプトアート']],
            'ステップアンドコンプリート' => ['one.json', [self::NAME =>  '永遠の放浪者', self::PROMOTYPE => 'S&C']],
            'ハロー・Foil' => ['mul.json', [self::NAME =>  '族樹の精霊、アナフェンザ', self::PROMOTYPE => 'ハロー・Foil']],
            'テキストレス・フルアート' => ['sch.json', [self::NAME => '月揺らしの騎兵隊', self::PROMOTYPE => 'テキストレス・フルアート']]
        ];
    }

    public function errorprovider() {
        return [
            'エキスパンションが存在しない' => ['not_found_ex.json', 441, 'NFDが登録されていません。'],
        ];
    }
}
