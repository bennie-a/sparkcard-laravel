<?php

namespace Tests\Feature\Unit;

use Illuminate\Http\Client\Response;
use Tests\TestCase;

use function PHPUnit\Framework\assertEmpty;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotEmpty;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNull;
use function Spatie\Ignition\ErrorPage\config;

class CardJsonFileTest extends TestCase
{
    const NAME = 'name';

    const PROMOTYPE = 'promotype';
    /**
     * テスト実行
     *
     * @dataProvider dataprovider
     */
    public function test_通常版(string $filename, array $expected)
    {
        $this->markTestSkipped("一時的に停止");
        $result = $this->execute($filename, $expected);
        $expectedname = $expected[self::NAME];
        $filterd = array_filter($result, function($a) use($expectedname){
            if ($a[self::NAME] == $expectedname && $a['isFoil'] == false) {
                return $a;
            }
        });
        $actualcard = current($filterd);
        assertNotEmpty($actualcard, '該当カードの有無');

        assertEquals($expected['multiverseId'], $actualcard['multiverseId'], 'multiverseId');
        assertEquals($expected['scryfallId'], $actualcard['scryfallId'], 'scryfallId');
    }

    /**
     * 特別版の特定に関するテスト
     * @dataProvider specialdataprovider
     */
    public function test_promotype(string $filename, array $expected) {
        $result = $this->execute($filename, $expected);
        $filterd = array_filter($result, function($a) use($expected){
            if ($a[self::NAME] == $expected[self::NAME] && $a[self::PROMOTYPE] == $expected[self::PROMOTYPE]) {
                return $a;
            }
        });
        $actualcard = current($filterd);
        assertNotEmpty($actualcard, '該当カードの有無');
    }

    private function execute(string $filename, array $expected) {
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
            '日本語表記あり' =>['war_short.json', ['name' => 'ジェイスの投影', 'multiverseId' => '463894', 'scryfallId' => '']],
            '日本語表記あり_multiverseIdなし' => ['mir.json', ['name' => '死後の生命', 'multiverseId' => '3476', 'scryfallId' => '']],
            '日本語表記なし' =>['test_color.json', ['name' => '飛空士の騎兵部隊', 'multiverseId' => '', 'scryfallId' => '38a62bb2-bc33-44d4-9a7e-92c9ea7d3c2c']],
            '両面カード' => ['mom.json' ,['name' => 'ラヴニカへの侵攻', 'multiverseId' => '',  'scryfallId' => '73f8fc4f-2f36-4932-8d04-3c2651c116dc']]
        ];
    }

    /**
     * テストデータ(特別版)
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
            'フルアート' =>  ['neo.json', [self::NAME =>  '平地', self::PROMOTYPE => 'フルアート']],
            'ネオンインク' => ['neo.json', [self::NAME =>  '貪る混沌、碑出告', self::PROMOTYPE => 'ネオンインク']],
            'ボーダレス「胆液」ショーケース' => ['one.json', [self::NAME =>  '機械の母、エリシュ・ノーン', self::PROMOTYPE => 'ボーダレス「胆液」ショーケース']],
            'コンセプトアート' => ['one.json', [self::NAME =>  '機械の母、エリシュ・ノーン', self::PROMOTYPE => 'コンセプトアート']],
            'ステップアンドコンプリート' => ['one.json', [self::NAME =>  '永遠の放浪者', self::PROMOTYPE => 'S&C']],
            'ハロー・Foil' => ['mul.json', [self::NAME =>  '族樹の精霊、アナフェンザ', self::PROMOTYPE => 'ハロー・Foil']],
        ];
    }
}
