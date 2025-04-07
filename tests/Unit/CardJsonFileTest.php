<?php

namespace Tests\Unit;

use App\Enum\CardColor;
use App\Facades\ScryfallServ;
use App\Models\CardInfo;
use Illuminate\Http\Response;
use Tests\TestCase;
use Tests\Trait\PostApiAssertions;
use Tests\Unit\Upload\AbstractCardJsonFileTest;

use function PHPUnit\Framework\assertEmpty;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotEmpty;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertNotSame;
use App\Services\Constant\CardConstant as Con;
use App\Services\Constant\StockpileHeader;

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

    /**
     * 特別版の特定に関するテスト
     * @dataProvider specialdataprovider
     */
    public function test_promotype(string $filename, array $expected) {
        $result = $this->execute($filename);
        $actualcard = $this->filtering_card($result, $expected);
        assertNotEmpty($actualcard, '該当カードの有無');
        assertEquals($expected[Con::PROMOTYPE], $actualcard[Con::PROMOTYPE], 'プロモタイプ');
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

    /**
     * プロモタイプ別のテスト
     *
     * @return array 各テストの入力値
     */
    public function specialdataprovider() {
        return [
            '日本限定カード' => ['war_short.json', [self::NAME => '群れの声、アーリン', Con::PROMOTYPE => '絵違い']],
            'ハロー・Foil' => ['mul.json', [self::NAME =>  '族樹の精霊、アナフェンザ', Con::PROMOTYPE => 'ハロー・Foil']],
            'ボーダレス' => ['mul.json', [self::NAME => '最後の望み、リリアナ', Con::PROMOTYPE => 'ボーダレス']],
        ];
    }

    public function promoProvider() {
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
     * 読み込みフィルターの確認テスト
     * @dataProvider filterProvider
     * @return void
     */
    public function test_uploadfilter(string $filename, bool $isDraft = false, string $color = '') {
        $result = $this->execute($filename, 201, $isDraft, $color);
        assertNotSame(0, count($result), '結果件数');
        foreach($result as $r) {
            if ($isDraft) {
                assertEmpty($r[Con::PROMOTYPE], '通常版の確認');
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
        $result = $this->execute($filename);
        $actual = $this->findCardById($result, 0, $scryfallId);
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

    private function execute(string $filename, bool $isDraft = false, ?string $color = '') {
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
            $response->assertStatus(Response::HTTP_CREATED);
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
        
        public function excludeprovider() {
            return [
                // 'イベント用プロモカード' => ['lci.json', 'Deep-Cavern Bat'],
            ];
        }

        protected function getSetCode():string
        {
            return 'COM';
        }
    
    }
