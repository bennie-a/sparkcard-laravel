<?php
namespace Tests\Unit\Upload;
use App\Models\Promotype;
use App\Models\Stockpile;
use App\Services\Constant\StockpileHeader;
use Tests\TestCase;
use App\Services\Constant\CardConstant as Column;
use Illuminate\Support\Facades\DB;
use Tests\Trait\PostApiAssertions;

/**
 * JSONファイル読み込みに関するAbstractクラス
 */
abstract class AbstractCardJsonFileTest extends TestCase{

    use PostApiAssertions;

    public function setUp():void {
        parent::setUp();
        $this->seed('TruncateAllTables');
        $this->seed('TestExpansionSeeder');
        $this->seed('DatabaseSeeder');
     }

    protected function ok(string $setcode, bool $isDraft = false, ?string $color = '') {
        $filename = strtolower($setcode).'.json';
        $json = $this->json_decode($filename);
        $cards = $json['data']['cards'];
        $data = [
            'data' => [
                'cards' => $cards,
                'code' => $json['data']['code']
            ]
        ];
        $query = sprintf('?setcode=%s&isDraft=%s&color=%s', $setcode, $isDraft, $color);
        $response = $this->upload_OK($query, $data);
        $actualCode = $response->json('setCode');
        $this->assertEquals($setcode, $actualCode, 'Ex略称');
        $result = $response->json('cards');
        return $result;
    }

    /**
     * 特別版が含まれているか検証する。
     * @dataProvider promoProvider
     */
    public function test_ok_promotype(string $number, string $expectedPromo) {
        $result = $this->ok($this->getSetCode(), false);
        $actualcard = $this->filteringCard($number, $result);
        $dbPromo = Promotype::findCardByAttr($expectedPromo);
        logger()->debug("期待プロモタイプ:{$dbPromo->name}");
        $this->assertEquals($dbPromo->name, $actualcard[Column::PROMOTYPE], 'プロモタイプ');
        $this->assertNotEmpty($actualcard[Column::SCRYFALLID], '{Column::SCRYFALLID}');
    }
    
    /**
     * 特定の特別版が含まれていないか検証する。
     * @dataProvider excludeProvider
     * @return void
     */
    public function test_ok_excluded(string $number) {
        $result = $this->ok($this->getSetCode(), false);
        $actualcard = $this->filteringCard($number, $result);
        $this->assertNull($actualcard, '特別版が存在する');
    }

    /**
     * 入力されたカード番号に該当するカード情報をアップロード結果から取得する。
     *
     * @param string $number カード番号
     * @param array $result アップロード結果
     * @return void
     */
    protected function filteringCard(string $number, array $result) {
        $filterd = array_filter($result, function($a) use($number){
            return $a[Column::NUMBER] == $number;
        });
        if (empty($filterd) == false) {
            $actualcard = current($filterd);
            return $actualcard;
        }
        return null;
    }
        
    /**
     * mutiverseIdかscryfallIdに該当するカード情報を取得する。
     *
     * @param array $result
     * @param string $multiId
     * @param string $scryId
     * @return array
     */
    protected function findCardById($result, string $multiId, string $scryId) {
        $filterd = array_filter($result, function($a) use($multiId, $scryId){
            if (!empty($multiId)) {
                if ($a[Column::MULTIVERSEID] == $multiId) {
                    return $a;
                }   
            } else if ($a[Column::SCRYFALLID] == $scryId) {
                return $a;
            }
        });
        $actualcard = current($filterd);
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
     * エンドポイントを取得する。
     *
     * @return string
     */
    public function getEndPoint() {
        return '/api/upload/card';
    }

    abstract protected function getSetCode():string;

    /**
     * 特定の特別版が含まれるか検証するテストデータ
     *
     * @return void
     */
    abstract public function promoProvider();

    /**
     * 特定の特別版が含まれないか検証するテストデータ
     *
     * @return void
     */
    abstract public function excludeProvider();
}