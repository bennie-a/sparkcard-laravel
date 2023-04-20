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
    /**
     * テスト実行
     *
     * @dataProvider dataprovider
     */
    public function test_通常版(string $filename, array $expected)
    {
        $actualcard = $this->execute($filename, $expected);
        assertEmpty($actualcard['promotype'], 'プロモタイプ');
    }

    /**
     * @dataProvider specialdataprovider
     */
    public function test_特別版(string $filename, array $expected) {
        $actualcard = $this->execute($filename, $expected);
        assertNotEmpty($actualcard['promotype'], 'プロモタイプ');
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
        $expectedname = $expected['name'];
        $filterd = array_filter($result, function($a) use($expectedname){
            if ($a['name'] == $expectedname && $a['isFoil'] == false) {
                return $a;
            }
        });
        $actualcard = current($filterd);
        assertNotEmpty($actualcard, '該当カードの有無');
        // logger()->debug($actualcard);
        assertEquals($expected['multiverseId'], $actualcard['multiverseId'], 'multiverseId');
        assertEquals($expected['scryfallId'], $actualcard['scryfallId'], 'scryfallId');

        return $actualcard;
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
            '両面カード' => ['neo.json', ['name' => '鏡割りの寓話', 'multiverseId' => '551741',  'scryfallId' => '6dee0388-a78d-4b3c-a0c5-25655e14115e']],
            'バトルカード' => ['mom.json' ,['name' => 'ラヴニカへの侵攻', 'multiverseId' => '',  'scryfallId' => '73f8fc4f-2f36-4932-8d04-3c2651c116dc']]
        ];
    }

    /**
     * テストデータ(特別版)
     *
     * @return array 各テストの入力値
     */
    public function specialdataprovider() {
        return [
            '日本限定カード' => ['war_short.json', ['name' => '群れの声、アーリン', 'multiverseId' => '',  'scryfallId' => '43261927-7655-474b-ac61-dfef9e63f428']],
        ];
    }
}
