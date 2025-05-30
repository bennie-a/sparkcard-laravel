<?php

namespace Tests\Unit;

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
     * @dataProvider dataprovider
     */
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
    public function dataprovider() {
        return [
            '日本語表記あり' =>['WAR', [Con::NUMBER => '272', 'multiverseId' => 463894, 'scryfallId' => 'c4d35a34-01b7-41e1-8491-a6589175d027']],
            '日本語表記あり_multiverseIdなし' => ['MIR', [Con::NUMBER => '1', 'multiverseId' => 0, 'scryfallId' => '4644694d-52e6-4d00-8cad-748899eeea84']],
            '日本語表記なし' =>['BRO',  [Con::NUMBER => '1', 'multiverseId' => 0, 'scryfallId' => '38a62bb2-bc33-44d4-9a7e-92c9ea7d3c2c']],
            '両面カード' => ['MH3' ,[Con::NUMBER => '242', 'multiverseId' => 0,  'scryfallId' => '2a717b98-cdac-416d-bf6c-f6b6638e65d1']],
        ];
    }

    /**
     * 土地カードのテスト
     * @dataProvider landProvider
     */
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
    public function landProvider() {
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

    public function promoProvider() {
        $this->markTestSkipped('各Exceptionクラスにて実施する');
        return [];
    }

    /**
     * Foilタイプの特定テスト
     * @dataProvider foiltypeprovider
     * @return void
     */
    public function test_finishes(string $setcode, string $number, array $foiltype) {

        $result = $this->ok($setcode);
        $actualcard = $this->filteringCard($number, $result);
        assertNotEmpty($actualcard, '該当カードの有無');
        $actualFoil = $actualcard['foiltype'];
        $this->assertSame($foiltype, $actualFoil, '仕様の特定');
    }

    public function foiltypeprovider() {
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
     * @dataProvider isDraftProvider
     * @return void
     */
    public function test_isDraft(string $setcode, bool $isDraft, callable $method) {
        $result = $this->ok($setcode, $isDraft);
        assertNotSame(0, count($result), '結果件数');
        foreach($result as $r) {
            $method($r);
        }
    }

    /**
     * 絞り込みのテストケース
     *
     * @return void
     */
    public function isDraftProvider() {
        return [
            '通常版フィルタがtrue' => ['WAR', true, $this->isOnlyDraft()],
            '通常版フィルタがfalse' => ['WAR', false, $this->hasPromotype()],
        ];
    }

    /**
     * カード情報が通常版かどうか検証する。
     *
     * @return boolean
     */
    private function isOnlyDraft() {
        return function($r) {
            $this->assertEmpty($r[Con::PROMOTYPE]);
        };
    }

    /**
     * カード情報に特別版が含まれるか検証する。
     *
     * @return boolean
     */
    private function hasPromotype() {
        return function($r) {
            $this->assertContains($r[Con::PROMOTYPE], ['', '絵違い']);
        };
    }


    /**
     * 色の判別を検証する
     * @dataProvider colorprovider
     * @return void
     */
    public function test_color(CardColor $color) {
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
    public function colorprovider() {
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
     * エラーケース
     *
     * @param string $filename
     * @param integer $exStatusCode
     * @param string $msgCode
     * @dataProvider errorprovider
     */
    public function test_error(string $setCode, int $exStatusCode, string $msgCode) {
        $response = $this->ng($setCode, 'not_found_ex.json', $exStatusCode);

        $expectedMsg = __($msgCode);
        $this->assertEquals($expectedMsg, $response['detail'], 'メッセージ');
    }
    
    public function errorprovider() {
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
        
        public function excludeprovider() {
            $this->markTestSkipped('各Exceptionクラスにて実施する');
            return [
                // 'イベント用プロモカード' => ['lci.json', 'Deep-Cavern Bat'],
            ];
        }

        protected function getSetCode():string
        {
            return 'COM';
        }
    
    }
