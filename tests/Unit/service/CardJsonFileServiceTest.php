<?php

namespace Tests\Unit\service;

use App\Enum\CardColor;
use App\Models\MainColor;
use App\Services\CardJsonFileService;
use Tests\TestCase;

use function PHPUnit\Framework\assertEmpty;
use function PHPUnit\Framework\assertEquals;

class CardJsonFileServiceTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_エキスパンション名()
    {   
        $contents = file_get_contents(storage_path("test/json/war_short.json"));
        $json = json_decode($contents, true);
        $service = new CardJsonFileService();
        $result = $service->build($json['data']);
        assertEquals("WAR", $result["setCode"]);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_日本語表記あり()
    {   
        $contents = file_get_contents(storage_path("test/json/war_short.json"));
        $json = json_decode($contents, true);
        $service = new CardJsonFileService();
        $result = $service->build($json['data']);
        $card = current($result["cards"]);
        assertEquals("規律の絆", $card['name']);
        assertEquals("462253", $card['multiverseId']);
        assertEmpty($card['scryfallId']);
        assertEquals("6", $card['number']);
        assertEmpty($card['promotype']);
    }

    public function test_日本限定カード() {
        $contents = file_get_contents(storage_path("test/json/war_short.json"));
        $json = json_decode($contents, true);
        $service = new CardJsonFileService();
        $result = $service->build($json['data']);
        $card = $result["cards"][4];
        assertEquals("群れの声、アーリン" ,$card['name']);
        assertEmpty($card['multiverseId']);
        assertEquals('43261927-7655-474b-ac61-dfef9e63f428', $card['scryfallId'], 'scryfallId');
        assertEquals("150", $card['number']);
        assertEquals("絵違い", $card['promotype']);

    }

    public function test_日本語表記なし()
    {
        $contents = file_get_contents(storage_path("test/json/test_color.json"));
        $json = json_decode($contents, true);
        $service = new CardJsonFileService();
        $result = $service->build($json['data']);
        $card = current($result["cards"]);
        assertEquals("飛空士の騎兵部隊", $card['name'], 'カード名(日本語)');
        assertEmpty($card['multiverseId']);
        assertEmpty($card['scryfallId']);
        assertEquals("1", $card['number']);
    }

    public function test_ブースターファン() {
        $contents = file_get_contents(storage_path("test/json/test_color.json"));
        $json = json_decode($contents, true);
        $service = new CardJsonFileService();
        $result = $service->build($json['data']);
        $target = $this->nextCard("Teferi, Temporal Pilgrim", $result["cards"]);
        assertEquals('ブースターファン', $target['promotype']);
    }

    public function test_色判定() {
        $contents = file_get_contents(storage_path("test/json/war_short.json"));
        $json = json_decode($contents, true);
        $service = new CardJsonFileService();
        $result = $service->build($json['data']);
        $colorKey = 'color';
        // 白
        $cards = $result['cards'];
        $white = current($cards);
        assertEquals('W', $white[$colorKey], '白');
        // 黒
        $black = $this->nextCard('鮮血の刃先', $cards);
        assertEquals('B', $black[$colorKey], '黒');
        // 青
        $blue = $this->nextCard('ジェイスの投影', $cards);
        assertEquals('U', $blue[$colorKey], '青');
        // 赤
        $red = $this->nextCard('炎の職工、チャンドラ', $cards);        
        assertEquals('R', $red[$colorKey], '赤');
        // 緑
        $green = $this->nextCard('群れの声、アーリン', $cards);        
        assertEquals('G', $green[$colorKey], '緑');
        // 多色
        $multi = $this->nextCard('龍神、ニコル・ボーラス', $cards);
        assertEquals('M', $multi[$colorKey], '多色');
        // 無色
        $less = $this->nextCard('大いなる創造者、カーン', $cards);
        assertEquals('L', $less[$colorKey], '無色');
        // アーティファクト
        $artifact = $this->nextCard('静かな潜水艇', $cards);
        assertEquals('A', $artifact[$colorKey], 'アーティファクト');
        // 土地
        $land = $this->nextCard('出現領域', $cards);
        assertEquals('Land', $land[$colorKey], '土地');


    }

    /**
     * 次の配列を取得する。
     *
     * @param [type] $cards
     * @return void
     */
    private function nextCard(string $name, $cards) {
        $target = array_filter($cards, function($c) use($name){
            return strcmp($name, $c['name']) == 0 || strcmp($name, $c['enname']) == 0;
        });
        return current($target);
    }
}