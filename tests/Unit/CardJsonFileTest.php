<?php

namespace Tests\Feature\Unit;

use App\Enum\CardColor;
use App\Models\Expansion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Response;
use Tests\TestCase;

use function PHPUnit\Framework\assertEmpty;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotEmpty;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNotSame;

/**
 * カード情報ファイルアップロードのテスト
 */
class CardJsonFileTest extends TestCase
{
    const NAME = 'name';

    const PROMOTYPE = 'promotype';

    const MULTIVERSEID = 'multiverseId';

    const SCRYFALLID = 'scryfallId';

    const EN_NAME = 'en_name';

    // use RefreshDatabase;
    public function setup():void
    {
        parent::setup();
        // $this->seed('DatabaseSeeder');
        // $this->seed('TestExpansionSeeder');

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
     * テストデータ(通常版)
     *
     * @return array 各テストの入力値
     */
    public function dataprovider() {
        return [
            '日本語表記あり' =>['war_short.json', ['name' => 'ジェイスの投影', 'multiverseId' => 463894, 'scryfallId' => 'c4d35a34-01b7-41e1-8491-a6589175d027', 'language' => 'JP']],
            '日本語表記あり_multiverseIdなし' => ['mir.json', ['name' => '死後の生命', 'multiverseId' => 0, 'scryfallId' => '4644694d-52e6-4d00-8cad-748899eeea84', 'language' => 'JP']],
            '日本語表記なし' =>['test_color.json', ['name' => '飛空士の騎兵部隊', 'multiverseId' => 0, 'scryfallId' => '38a62bb2-bc33-44d4-9a7e-92c9ea7d3c2c', 'language' => 'JP']],
            '両面カード' => ['mh3.json' ,['name' => '知りたがりの学徒、タミヨウ', 'multiverseId' => 0,  'scryfallId' => '2a717b98-cdac-416d-bf6c-f6b6638e65d1',  'language' => 'JP']],
            'ファイレクシア語' => ['neo.json', ['name' => '発展の暴君、ジン＝ギタクシアス', 'multiverseId' => 0,  'scryfallId' => 'ffa7cbf8-64b2-428e-8991-d8454d724f9f', 'language' => 'PH']],
            '出来事付きカード' => ['woe.json', ['name' => '恋に落ちた騎士', self::MULTIVERSEID => 0,  'scryfallId' => '5980a930-c7f8-45e1-a18a-87734d9ed09e', 'language' => 'JP']],
        ];
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
        assertEquals(CardColor::LAND->value, $actualcard['color'], '色コード');
    }

    /**
     * カードタイプ別のテストデータ
     *
     * @return void
     */
    public function cardtypeProvider() {
        return [
            '基本土地' => ['land.json', ['name' => '島(263)', self::MULTIVERSEID => 604650]],
            '特殊土地' => ['land.json', ['name' => 'やせた原野', self::MULTIVERSEID => 465455]],
            'フルアート版土地' => ['land.json', ['name' => '平地(262)', self::MULTIVERSEID => 604649]],
            '冠雪土地' => ['land.json', ['name' => '冠雪の島', self::MULTIVERSEID => 465470]],
        ];
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
            'テキストレス・フルアート' => ['sch.json', [self::NAME => '月揺らしの騎兵隊', self::PROMOTYPE => 'テキストレス・フルアート']],
            'ボーダレス' => ['mul.json', [self::NAME => '最後の望み、リリアナ', self::PROMOTYPE => 'ボーダレス']],
            'おとぎ話' => ['wot.json', [self::NAME => '盲従', self::PROMOTYPE => 'おとぎ話']],
            'アニメ・ボーダレス' => ['wot.json', [self::NAME => '騙し討ち', self::PROMOTYPE => 'アニメ・ボーダレス']],
            '「事件簿」ショーケース' => ['mkm.json', [self::NAME => '古き神々の咆哮、ヤラス', self::PROMOTYPE => '「事件簿」ショーケース']],
            '「拡大鏡」ショーケース' => ['mkm.json', [self::NAME => '戦導者の号令', self::PROMOTYPE => '「拡大鏡」ショーケース']],
            '大都市ラヴニカ' => ['mkm.json', [self::NAME => '顔を繕う者、ラザーヴ', self::PROMOTYPE => '大都市ラヴニカ']],
            '旧枠' => ['mh3.json', [self::NAME => '護衛募集員', self::PROMOTYPE => '旧枠']],
            '「プロファイル」ボーダーレス' => ['mh3.json', [self::NAME => '皇国の相談役、真珠耳', self::PROMOTYPE => '「プロファイル」ボーダーレス']],
            '「フレームブレイク」ボーダーレス' => ['mh3.json', [self::NAME => '巨大なるカーリア', self::PROMOTYPE => '「フレームブレイク」ボーダーレス']],
            '「コンセプトアート」ボーダーレス版エルドラージ' =>
                     ['mh3.json', [self::NAME => '再誕世界、エムラクール', self::PROMOTYPE => 'コンセプトアート']],
        ];
    }

    /**
     * Foilタイプの特定テスト
     * @dataProvider foiltypeprovider
     * @return void
     */
    public function test_finishes(string $filename, int $number, array $foiltype) {
        // $this->markTestSkipped('一時スキップ');

        $result = $this->execute($filename);
        $filterd = array_filter($result, function($a) use($number){
            if ($a['number'] == $number) {
                return $a;
            }
        });
        $actualcard = current($filterd);
        assertNotEmpty($actualcard, '該当カードの有無');
        $actualFoil = $actualcard['foiltype'];
        $this->assertSame($foiltype, $actualFoil, '仕様の特定');
    }

    public function foiltypeprovider() {
        return [
            '通常版&Foil' => ['mul.json', 1, ["通常版", "Foil"]],
            'ハロー・Foil' => ['mul.json', 131, ["ハロー・Foil"]],
            'エッチングFoil' => ['mul.json', 66, ["エッチングFoil"]],
            'S&C・Foil' => ['one.json', 422, ['S&C・Foil']],
            'オイルスリックFoil' => ['one.json', 345, ['Foil']],
            'テクスチャーFoil' => ['mul.json', 573,[ 'テクスチャーFoil']],
            'コンフェッティFoil' => ['wot.json', 90,[ 'コンフェッティFoil']]
        ];
    }

    /**
     * 読み込みフィルターの確認テスト
     * @dataProvider filterProvider
     * @return void
     */
    public function test_uploadfilter(string $filename, bool $isDraft = false, string $color = '') {
        // $this->markTestSkipped('一時スキップ');
        $result = $this->execute($filename, 201, $isDraft, $color);
        assertNotSame(0, count($result), '結果件数');
        foreach($result as $r) {
            if ($isDraft) {
                assertEmpty($r[self::PROMOTYPE], '通常版の確認');
            }
            if (!empty($color)) {
                assertEquals($color, $r['color'], '指定した色の確認');
            }
        }
    }

    /**
     * フィルターのデータ
     *
     * @return void
     */
    public function filterProvider() {
        return [
            '通常版フィルターのみ' => ['war_short.json', true, ''],
            '色フィルターのみ' => ['war_short.json', false, 'B'],
            '通常版フィルターと色フィルター両方' => ['war_short.json', true, 'B']
        ];
    }

    /**
     * 色の判別を検証する
     * @dataProvider colorprovider
     * @return void
     */
    public function test_color(string $filename, string $name, string $scryfallId, string $color) {
        // $this->markTestSkipped('一時スキップ');
        $result = $this->execute($filename);
        $actual = $this->findCard($result, 0, $scryfallId);
        assertNotNull($actual, "該当カード");
        assertEquals($name, $actual[self::NAME], "カード名");
        assertEquals($color, $actual['color']);
    }

    /**
     * カードの色に関するテストデータ
     * (土地はcardtypeにて別途テスト)
     *
     * @return void
     */
    public function colorprovider() {
        return [
            '白' => ['war_short.json', '規律の絆', 'bb7c78bb-9f2a-47a4-adc4-b497bb38f46f', 'W'],
            '黒' => ['war_short.json', '鮮血の刃先', 'ac82422c-8ac8-4fbb-b9b9-d0aa23dded61', 'B'],
            '青' => ['war_short.json', 'ジェイスの投影', 'c4d35a34-01b7-41e1-8491-a6589175d027', 'U'],
            '赤' => ['war_short.json', '炎の職工、チャンドラ', 'd21a7b23-8827-49f2-ade4-75a602d17743', 'R'],
            '緑' => ['war_short.json', '群れの声、アーリン', '43261927-7655-474b-ac61-dfef9e63f428', 'G'],
            '多色' => ['war_short.json', '龍神、ニコル・ボーラス', '98b68dea-a7be-4f99-8a50-4c8cf0e0f7a9', 'M'],
            'アーティファクト' => ['war_short.json', '静かな潜水艇', 'ae2f3dee-1768-4562-9333-a50b9ee7570f', 'A'],
            '無色' => ['war_short.json', '大いなる創造者、カーン', '3ec0c0fb-1a4f-45f4-85b7-346a6d3ce2c5', 'L'],
            '単色の出来事付きカード' => ['woe.json', '恋に落ちた騎士', '5980a930-c7f8-45e1-a18a-87734d9ed09e', 'W'],
            '多色の出来事付きカード' => ['woe.json', 'イモデーンの徴募兵', '4dbaa855-3f8e-42e6-8ec8-5ffbc5c8acf0', 'M']
        ];
    }

    /**
     * エラーケース
     *
     * @param string $filename
     * @param integer $expectedCode
     * @param string $expectedMsg
     * @dataProvider errorprovider
     */
    public function test_error(string $filename, int $expectedCode, string $expectedMsg) {
        // $this->markTestSkipped('一時スキップ');
        $response = $this->execute($filename, $expectedCode);
        assertEquals($expectedMsg, $response->json('detail'), 'メッセージ');
    }

    /**
     * 除外カードのテストケース
     * @dataProvider excludeprovider
     */
    public function test_除外カード(string $filename, string $excludedname) {
        // $this->markTestSkipped('一時スキップ');
        $result = $this->execute($filename);
        $filterd = array_filter($result, function($a) use($excludedname){
            if ($a[self::EN_NAME] == $excludedname) {
                return $a;
            }
        });

        assertEmpty($filterd, '除外カードがある');
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
    private function execute(string $filename, int $statuscode = 201, bool $isDraft = false, string $color = '') {
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
        $query = sprintf('?isDraft=%s&color=%s', $isDraft, $color);
        $response = $this->post('/api/upload/card'.$query, $data, $header);
        logger()->error($response->json('detail'));
        $response->assertStatus($statuscode);
        if ($statuscode == 201) {
            assertEquals($data['data']['code'], $response->json('setCode'), 'Ex略称');
            $result = $response->json('cards');
            return $result;
        }
        return $response;
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

    public function errorprovider() {
        return [
            'エキスパンションが存在しない' => ['not_found_ex.json', 441, 'NFDが登録されていません。'],
        ];
    }

    public function excludeprovider() {
        return [
            '出来事ソーサリー' => ['woe.json', 'Betroth the Beast'],
            '不可視インク仕様' => ['mkm.json', 'Aurelia\'s Vindicator'],
            'シリアル番号付き' => ['mkm.json', 'Aurelia, the Law Above'],
            'イベント用プロモカード' => ['lci.json', 'Deep-Cavern Bat']

        ];
    }
}
