<?php

namespace Tests\Unit\service;

use App\Models\Expansion;
use App\Services\CardJsonFileService;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotEmpty;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

class CardJsonFileServiceTest extends TestCase
{
    public function setup():void
    {
        parent::setup();
        $this->war = Expansion::factory()->createOne(['name' => '灯争大戦','attr' => 'WAR']);
        $this->bro = Expansion::factory()->createOne(['name' => '第4版', 'attr' => '4ED']);
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
    }

    public function test_通常版とFoil版取得() {
        // 通常版とFoil版のデータを別に登録する。
        $contents = file_get_contents(storage_path("test/json/war_short.json"));
        $json = json_decode($contents, true);
        $service = new CardJsonFileService();
        $result = $service->build($json['data']);

        $cards = $result['cards'];
        $nonFoils = array_filter($cards, function($c) {
            return $c['isFoil'] == false;
        });
        $foils = array_filter($cards, function($c) {
            return $c['isFoil'] == true;
        });
        assertNotEmpty($nonFoils, '通常版の有無');
        assertSame(count($nonFoils), 9);
        assertNotEmpty($foils, 'Foil版の有無');
        assertSame(count($foils), 8);
    }

    public function test_初期セット() {
        $contents = file_get_contents(storage_path("test/json/4ED.json"));
        $json = json_decode($contents, true);
        $service = new CardJsonFileService();
        $result = $service->build($json['data']);
        $cards = $result['cards'];
        assertNotEmpty($cards);
        assertTrue(count($cards) > 1);
    }

    /**
     * 指定したJSONファイルを読み込む。
     *
     *
     * @param string $file
     * @return array
     */
    private function build($file) {
        $contents = file_get_contents(storage_path("test/json/".$file));
        $json = json_decode($contents, true);
        $service = new CardJsonFileService();
        $result = $service->build($json['data']);
        $cards = $result['cards'];
        return $cards;
    }
    /**
     * 次の配列を取得する。
     *
     * @param [type] $cards
     * @return void
     */
    private function nextCard(string $name, array $cards) {
        $target = array_filter($cards, function($c) use($name){
            return $c['isFoil'] == false && (strcmp($name, $c['name']) == 0 || strcmp($name, $c['en_name']) == 0);
        });
        return current($target);
    }

    public function tearDown():void
    {
        Expansion::query()->delete();
    }

}
