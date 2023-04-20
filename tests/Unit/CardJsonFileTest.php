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
        $response = $this->execute($filename);
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
        assertEmpty($actualcard['promotype'], 'プロモタイプ');
        assertEquals($expected['multiverseId'], $actualcard['multiverseId'], 'multiverseId');
    }

    private function execute(string $filename) {
        $header = [
            'headers' => [
                "Content-Type" => "application/json",
            ]
        ];
        $json = $this->json_decode($filename);
        $expected = $json['data']['cards'];
        $data = [
            'data' => [
                'cards' => $expected,
                'code' => $json['data']['code']
            ]
        ];
        $response = $this->post('/api/upload/card', $data, $header);
        $response->assertStatus(201);
        assertEquals($data['data']['code'], $response->json('setCode'), 'Ex略称');
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

    /**
     * テストデータ(通常版)
     *
     * @return array 各テストの入力値
     */
    public function dataprovider() {
        return [
            '日本語表記あり' =>['war_short.json', ['name' => 'ジェイスの投影', 'multiverseId' => '463894']],
            '日本語表記あり_multiverseIdなし' => ['mir.json', ['name' => '死後の生命', 'multiverseId' => '3476']],
            // 'バトルカード' => []
        ];
    }

    public function colordataprovider() {
        return [];
    }
}
