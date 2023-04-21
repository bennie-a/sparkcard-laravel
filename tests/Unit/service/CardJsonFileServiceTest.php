<?php

namespace Tests\Unit\service;

use App\Enum\CardColor;
use App\Enum\PromoType;
use App\Models\MainColor;
use App\Services\CardJsonFileService;
use Tests\TestCase;

use function PHPUnit\Framework\assertEmpty;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotEmpty;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

class CardJsonFileServiceTest extends TestCase
{
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

    public function test_拡張アート() {
        $cards = $this->build("neo.json");
        $target = $this->getCardByNumber('445', $cards);
        assertEquals(PromoType::EXTENDEDART->text(), $target['promotype'], "プロモタイプ");
    }

    public function test_ショーケース() {
        $cards = $this->build("neo.json");
        $target = $this->getCardByNumber('371', $cards);
        assertEquals(PromoType::SHOWCASE->text(), $target['promotype'], "プロモタイプ");
    }

    public function test_ブースターファン() {
        $contents = file_get_contents(storage_path("test/json/test_color.json"));
        $json = json_decode($contents, true);
        $service = new CardJsonFileService();
        $result = $service->build($json['data']);
        $target = $this->nextCard("Teferi, Temporal Pilgrim", $result["cards"]);
        assertEquals('ブースターファン', $target['promotype']);
    }

    public function test_フルアート版土地() {
        $cards = $this->build("neo.json");
        assertNotEmpty($cards);
        $actual = $this->nextCard("平地", $cards);
        assertNotNull($actual, "土地カードの有無");
        assertEquals("フルアート", $actual["promotype"], "フルアート");
    }

    public function test_framesEffectsに該当なし() {
        $cards = $this->build("neo.json");
        $target = $this->getCardByNumber('406', $cards);
        assertEquals(PromoType::BOOSTER_FAN->text(), $target['promotype'], "プロモタイプ");

    }

    // public function test_ファイレクシア語() {
    //     $cards = $this->build("neo.json");
    //     $target = $this->getCardByNumber('427', $cards);
    //     assertEquals(PromoType::SHOWCASE->text(), $target['promotype'], "プロモタイプ");
    //     assertEquals("PH", $target['language'], "言語");
    // }

    public function test_ネオンインク版() {
        $cards = $this->build("neo.json");
        // 貪る混沌、碑出告
        $target = $this->getFoilCard('432', $cards);
        assertEquals(PromoType::NEONINK->text(), $target['promotype'], "プロモタイプ");
    }

    public function test_胆液版() {
        $cards = $this->build("one.json");
        $target = $this->getFoilCard('345', $cards);
        assertEquals(PromoType::OILSLICK->text(), $target['promotype'], "プロモタイプ");
    }

    public function test_コンセプトアート() {
        $cards = $this->build("one.json");
        $target = $this->getFoilCard('416', $cards);
        assertEquals(PromoType::CONCEPT->text(), $target['promotype'], "プロモタイプ");
    }

    public function test_ステップアンドコンプリート() {
        $cards = $this->build("one.json");
        $target = $this->getFoilCard('422', $cards);
        assertEquals(PromoType::STEPANDCOMPLEAT->text(), $target['promotype'], "プロモタイプ");

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

    private function getCardByNumber(string $number, array $cards) {
        $filterd = array_filter($cards, function($c) use($number) {
            return $number == $c['number'] && $c['isFoil'] == false;
        });
        logger()->debug($cards);
        assertNotEmpty($filterd, 'カード情報の有無');
        assertEquals(1, count($filterd));
        return current($filterd);
    }

    private function getFoilCard(string $number, $cards) {
        $filterd = array_filter($cards, function($c) use($number) {
            return $number == $c['number'] && $c['isFoil'] == true;
        });
        logger()->debug($cards);
        assertNotEmpty($filterd, 'カード情報の有無');
        assertEquals(1, count($filterd));
        return current($filterd);
    }
}
