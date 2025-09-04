<?php

namespace Tests\Unit\Upload;

use App\Enum\CardColor;
use Illuminate\Http\Response;
use Tests\Trait\PostApiAssertions;
use Tests\Unit\Upload\AbstractCardJsonFileTest;

use function PHPUnit\Framework\assertEmpty;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotEmpty;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNotSame;
use App\Services\Constant\CardConstant as Con;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * カード情報ファイルアップロードのテスト
 */
class CardJsonFileTest extends AbstractCardJsonFileTest
{
    use PostApiAssertions;

    const NAME = 'name';

    const EN_NAME = 'en_name';


    /**
     * 土地以外のカードについて検証する。
     *
     */
    #[DataProvider('dataprovider')]
    public function test_通常版(string $setcode, array $expected)
    {
        $result = $this->ok($setcode);
        $exMultiverseId = $expected[Con::MULTIVERSEID];
        $exScryId = $expected[Con::SCRYFALLID];

        $number = $expected[Con::NUMBER];
        $actualcard = $this->filteringCard($expected[Con::NUMBER], $result);
        assertNotEmpty($actualcard, '該当カードの有無');

        assertNotNull($actualcard[self::NAME], 'カード名');
        assertEquals($number, $actualcard[Con::NUMBER], 'カード番号');
        assertEquals($exMultiverseId, $actualcard[Con::MULTIVERSEID], 'multiverseId');
        assertEquals($exScryId, $actualcard[Con::SCRYFALLID], 'scryfallId');
        assertEmpty($actualcard[Con::PROMOTYPE]);
    } 
    
    /**
     * テストデータ(通常版)
     *
     * @return array 各テストの入力値
     */
    public static function dataprovider() {
        return [
            '日本語表記あり' =>['WAR', [Con::NUMBER => '272', 'multiverseId' => 463894, 'scryfallId' => 'c4d35a34-01b7-41e1-8491-a6589175d027']],
            '日本語表記あり_multiverseIdなし' => ['MIR', [Con::NUMBER => '1', 'multiverseId' => 0, 'scryfallId' => '4644694d-52e6-4d00-8cad-748899eeea84']],
            '日本語表記なし' =>['BRO',  [Con::NUMBER => '1', 'multiverseId' => 0, 'scryfallId' => '38a62bb2-bc33-44d4-9a7e-92c9ea7d3c2c']],
            '両面カード' => ['MH3' ,[Con::NUMBER => '242', 'multiverseId' => 0,  'scryfallId' => '2a717b98-cdac-416d-bf6c-f6b6638e65d1']],
        ];
    }

    /**
     * 土地カードのテスト
     * 
     */
    #[DataProvider('landProvider')]
    public function test_土地カード(array $expected) {
        $setCode = 'MH1';
        $result = $this->ok($setCode);
        $actualcard = $this->findCardById($result, $expected[Con::MULTIVERSEID], '');
        assertNotEmpty($actualcard, '結果の有無');
        assertEquals($expected[self::NAME], $actualcard[self::NAME], 'カード名');
        assertEquals(CardColor::LAND->value, $actualcard[Con::COLOR], '色');
    }
    
    /**
     * カードタイプ別のテストデータ
     *
     * @return void
     */
    public static function landProvider() {
        return [
            '基本土地' => [['name' => '島(263)', Con::MULTIVERSEID => 604650]],
            '特殊土地' => [ ['name' => 'やせた原野', Con::MULTIVERSEID => 465455]],
            '冠雪土地' => [ ['name' => '冠雪の島', Con::MULTIVERSEID => 465470]],
        ];
    }

    protected function filtering_card(array $result, array $expected) {
        $filterd = array_filter($result, function($a) use($expected){
            if ($a[self::NAME] == $expected[self::NAME] && $a[Con::PROMOTYPE] == $expected[Con::PROMOTYPE]) {
                return $a;
            }
        });
        $actualcard = current($filterd);
        return $actualcard;
    }

    public function test_ok_promotype(string $number, string $promo_attr) {
        $this->markTestSkipped("テスト対象外のためスキップ");        
    }
    
    public static function promoProvider() {
        return [];
    }

    /**
     * Foilタイプの特定テスト
     * @return void
     */
    #[DataProvider('foiltypeprovider')]
    public function test_finishes(string $setcode, string $number, array $foiltype) {

        $result = $this->ok($setcode);
        $actualcard = $this->filteringCard($number, $result);
        assertNotEmpty($actualcard, '該当カードの有無');
        $actualFoil = $actualcard['foiltype'];
        $this->assertSame($foiltype, $actualFoil, '仕様の特定');
    }

    public static function foiltypeprovider() {
        return [
            '通常版&Foil' => ['MUL', "1", ["通常版", "Foil"]],
            'ハロー・Foil' => ['MUL', "131", ["ハロー・Foil"]],
            'エッチングFoil' => ['MUL', "66", ["エッチングFoil"]],
            'S&C・Foil' => ['ONE', "422", ['S&C・Foil']],
            'オイルスリックFoil' => ['ONE', "345", ['Foil']],
        ];
    }

    /**
     * 検索条件に関するテストケース
     * @return void
     */
    public function test_通常版フィルタがtrue() {
        $result = $this->ok('WAR', true);
        assertNotSame(0, count($result), '結果件数');
        foreach($result as $r) {
            $this->assertEquals(1, $r[Con::PROMO_ID], 'プロモタイプが通常版以外');
        }
    }

    public function test_通常版フィルタがfalse() {
        $result = $this->ok('WAR', false);
        assertNotSame(0, count($result), '結果件数');
        foreach($result as $r) {
            $this->assertContains($r[Con::PROMO_ID], [1, 11]);
        }
    }

    /**
     * 色の判別を検証する
     * @return void
     */
    #[DataProvider('colorprovider')]
    public function test_色フィルター(CardColor $color) {
        $result = $this->ok('WAR', false, $color->value);
        foreach($result as $r) {
            $this->assertEquals($color->value, $r[Con::COLOR], '違う色が抽出された');
        }
    }

    /**
     * カードの色に関するテストデータ
     * (土地はcardtypeにて別途テスト)
     *
     * @return void
     */
    public static function colorprovider() {
        return [
            '色フィルター_白' => [CardColor::WHITE],
            '色フィルター_黒' => [CardColor::BLACK],
            '色フィルター_青' => [CardColor::BLUE],
            '色フィルター_赤' => [CardColor::RED],
            '色フィルター_緑' => [CardColor::GREEN],
            '色フィルター_多色' => [CardColor::MULTI],
            '色フィルター_茶' => [CardColor::ARTIFACT],
            '色フィルター_無色' => [CardColor::LESS],
        ];
    }

    /**
     * 色の判別に関するテスト
     */
    #[DataProvider('cardColorProvider')]
    public function test_色判別(string $number, CardColor $color) {
        $this->function_verify_color('WAR', $number, $color);
    }

    private function function_verify_color(string $setCode, string $number, CardColor $color) {
        $result = $this->ok($setCode);
        $actualcard = $this->filteringCard($number, $result);
        assertNotNull($actualcard, '該当カードの有無');
        assertEquals($color->value, $actualcard[Con::COLOR], '色の判別');
    }

    public static function cardColorProvider():array
    {
        return [
            '単色_白' => ['6', CardColor::WHITE],
            '単色_青' => ['272', CardColor::BLUE],
            '単色_黒' => ['78', CardColor::BLACK],
            '単色_赤' => ['119', CardColor::RED],
            '単色_緑' => ['150', CardColor::GREEN],
            '多色' => ['207', CardColor::MULTI],
            '無色' => ['1', CardColor::LESS],
            '色なしアーティファクト' => ['241', CardColor::ARTIFACT],
            '土地' => ['245', CardColor::LAND],
        ];
    }

    /**
     * 
     */
    #[DataProvider('artifactColorProvider')]
    public function test_色付きアーティファクト(string $number, CardColor $color) {
        $this->function_verify_color('EOE', $number, $color);
    }

    public static function artifactColorProvider():array
    {
        return [
            '白' => ['32', CardColor::WHITE],
            '青' => ['52', CardColor::BLUE],
            '黒' => ['106', CardColor::BLACK],
            '赤' => ['131', CardColor::RED],
            '緑' => ['179', CardColor::GREEN],
            '多色' => ['219', CardColor::MULTI],
            '土地' => ['267', CardColor::LAND],
        ];
    }

    /**
     * エラーケース
     *
     * @param string $filename
     * @param integer $exStatusCode
     * @param string $msgCode
     * 
     */
    #[DataProvider('errorprovider')]
    public function test_error(string $setCode, int $exStatusCode, string $msgCode) {
        $response = $this->ng($setCode, 'not_found_ex.json', $exStatusCode);

        $expectedMsg = __($msgCode);
        $this->assertEquals($expectedMsg, $response['detail'], 'メッセージ');
    }
    
    public static function errorprovider() {
        return [
            'エキスパンションが存在しない' => ['NFD', Response::HTTP_BAD_REQUEST,  'messages.setcode-notFound'],
            'JSONファイルとエキスパンションと入力したエキスパンションが異なる' =>
                                                         ['WAR', Response::HTTP_BAD_REQUEST,  'messages.setcode-different'],
        ];
    }

    private function ng(string $setCode, string $filename, int $statusCode) {
        $json = $this->json_decode($filename);
        $cards = $json['data']['cards'];
        $data = [
            'data' => [
                'cards' => $cards,
                'code' => $json['data']['code']
            ]
        ];
        $query = sprintf('?setcode=%s', $setCode);
        $response = $this->upload($query, $data);
        $response->assertStatus($statusCode);
        return $response->json();
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
        
        public function test_ok_excluded(string $number) {
            $this->markTestSkipped("テスト対象外のためスキップ");
        }

        public static function excludeprovider() {
            return [
                // 'イベント用プロモカード' => ['lci.json', 'Deep-Cavern Bat'],
            ];
        }

        protected function getSetCode():string
        {
            return 'COM';
        }
    
    }
