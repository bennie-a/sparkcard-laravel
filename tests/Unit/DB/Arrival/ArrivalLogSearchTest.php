<?php

namespace Tests\Unit\DB\Arrival;

use App\Models\ArrivalLog;
use App\Models\Promotype;
use App\Models\Stockpile;
use Tests\TestCase;
use App\Services\Constant\ArrivalConstant as ACon;
use App\Services\Constant\CardConstant as Con;
use App\Services\Constant\StockpileHeader as Header;
use App\Services\Constant\SearchConstant as SCon;
use App\Services\Constant\GlobalConstant as GCon;
use Illuminate\Http\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Database\Seeders\Arrival\TestArrivalLogSeeder;
use Tests\Database\Seeders\DatabaseSeeder;
use Tests\Database\Seeders\TestCardInfoSeeder;
use Tests\Database\Seeders\TestStockpileSeeder;
use Tests\Database\Seeders\TruncateAllTables;
use Tests\Trait\GetApiAssertions;
use Tests\Util\TestDateUtil;

/**
 * エンドポイントが'/api/arrival'のテストクラス
 */
class ArrivalLogSearchTest extends TestCase
{
    use GetApiAssertions {
        GetApiAssertions::verifyCard as verifyCardFromParent;
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(TruncateAllTables::class);
        $this->seed(DatabaseSeeder::class);
        $this->seed(TestCardInfoSeeder::class);
        $this->seed(TestStockpileSeeder::class);
        $this->seed(TestArrivalLogSeeder::class);
    }

    public function test_条件が入荷日と取引先カテゴリ() {
        $condition = [ACon::ARRIVAL_DATE => TestDateUtil::formatToday(), SCon::VENDOR_TYPE_ID => 1];
        $response = $this->assert_OK($condition);
        $json = $response->json();
        
        $this->assertArrayHasKey(GCon::DATA, $json, 'data要素が含まれない');
        $data = $json[GCon::DATA];
        $this->assertArrayHasKey(ACon::ARRIVAL_DATE, $data, '入荷日が含まれない');
        $this->assertEquals(TestDateUtil::formatToday(), $data[ACon::ARRIVAL_DATE], '入荷日が一致しない');
    }
    
    public function test_条件がカード名と取引先カテゴリ() {
        $condition = [SCon::CARD_NAME => 'ドラゴン', SCon::VENDOR_TYPE_ID => 3];
        $response = $this->assert_OK($condition);
        $json = $response->json();
        $this->assertArrayNotHasKey(ACon::ARRIVAL_DATE, $json[GCon::DATA], '入荷日が含まれている');
    }
    
    /**
     * 取引先カテゴリについて検証する。
     * @param integer $vendor_type_id
     * @param callable $method
     * @return void
     */
    private function assertVendor(int $vendor_type_id, callable $method) {
        $condition = [SCon::VENDOR_TYPE_ID => $vendor_type_id,
                         ACon::ARRIVAL_DATE => TestDateUtil::formatToday()];
        $response = $this->assert_OK($condition);
        $json = $response->json();
        $this->assertArrayHasKey(GCon::DATA, $json, 'data要素が含まれない');

        $data = $json[GCon::DATA];
        $this->assertArrayHasKey(ACon::VENDOR, $data, 'vendor要素が含まれない');

        $method($data[ACon::VENDOR]);
    }
    
    #[DataProvider('vendorProvider')]
    public function test_otherVendor(int $vendor_type_id) {
        $this->assertVendor($vendor_type_id, $this->verifyOtherVendor());
    }
    
    /**
     * 取引先に関するテストケース
     */
    public static function vendorProvider() {
        return [
                '入荷先カテゴリがオリジナルパック' => [1],
                '入荷先カテゴリが私物' => [2],
                '入荷先カテゴリが棚卸し' => [4],
                '入荷先カテゴリが返品' => [5],
        ];
    }
    
    public function test_入荷先カテゴリが買取() {
        $this->assertVendor(3, $this->verifyBuyVendor());
    }

    public function test_カード情報が通常版() {
        $this->assertIsFoil('ドロスの魔神', $this->verifyNonFoil());
    }
    
    #[DataProvider('isFoilProvider')]
    public function test_カード情報がFoil版(string $name) {
        $this->assertIsFoil($name, $this->verifyFoil());
    }

    public static function isFoilProvider() {
        return [
            'カード情報がFoil版' => ['告別'],
            'カード情報が特殊Foil版' => ['機械の母、エリシュ・ノーン']
        ];
    }
    
    /**
     * カードのFoil要素について検証する。
     * @return void
     */
    private function assertIsFoil(string $name, callable $method) {
        $condition = [SCon::CARD_NAME => $name, SCon::VENDOR_TYPE_ID => 1];
        $this->assertResult($condition, $method);
    }

    /**
     * card要素のpromotypeについて検証する。
     *
     * @return void
     */
    #[DataProvider('promoProvider')]
    public function test_promotype(string $name, int $promo_id) {
        $condition = [SCon::CARD_NAME => $name, SCon::VENDOR_TYPE_ID => 1];
        $response = $this->assert_OK($condition);
        $json = $response->json();
        $card = current($json[GCon::LOGS])[Con::CARD];
        $this->assertArrayHasKey(Con::PROMOTYPE, $card, 'promotype要素が含まれない');

        $actual = $card[Con::PROMOTYPE];
        $this->assertEquals($promo_id, $actual[GCon::ID], 'promotypeの値が一致しない');
        $db = Promotype::find($promo_id);
        $this->assertEquals($db->name, $actual[GCon::NAME], 'promotypeの名前が一致しない');
    }

    public static function promoProvider() {
        return [
            '通常版' => ['ドラゴンの運命', 1],
            '特別版' => ['告別', 2]
        ];
    }
    
    protected function assertResult(array $condition, callable $method) {
        $response = $this->assert_OK($condition);
        $json = $response->json();
        
        $this->assertArrayHasKey(GCon::LOGS, $json, 'logs要素が含まれない');

        $this->assertNotEmpty($json[GCon::LOGS], 'log要素の有無');
        foreach($json[GCon::LOGS] as $j) {
            $id = $j[GCon::ID];
            logger()->debug('入荷ID:'.$id);
            $this->assertNotEmpty($id, '入荷ID');

            $log = ArrivalLog::find($j[GCon::ID]);
            $this->assertEquals($log->cost, $j[Header::COST], '原価');
            logger()->debug("原価：expected:{$log->cost}, actual:{$j[Header::COST]}");

            $this->assertEquals($log->alog_quan, $j[Header::QUANTITY], '枚数');
            $this->verifyCard($log->stock_id, $j[Con::CARD]);
            $method($condition, $j, $log);
        }
    }

    /**
     * 検索結果が無い場合のテストケース
     *
     * @return void
     */
    public function test_NoResult() {
        $condition = [ACon::ARRIVAL_DATE => TestDateUtil::formatFourDateBefore(), SCon::VENDOR_TYPE_ID => 1];
        $this->assert_NG($condition, Response::HTTP_NOT_FOUND, '検索結果がありません。');
    }

    /**
     * その他NGケース
     * @return void
     */
    #[DataProvider('ngProvider')]
    public function test_NG(array $condition, string $message) {
        $this->assert_NG($condition, Response::HTTP_BAD_REQUEST, $message);
    }
    
    public static function ngProvider() {
        return [
            '取引先カテゴリIDが未入力' => [[], '取引先カテゴリIDは必ず入力してください。'],
            '入荷日が日付形式ではない' => 
                    [[ACon::ARRIVAL_DATE => 'aaa', SCon::VENDOR_TYPE_ID => 1], '入荷日が日付形式ではありません。']
        ];
    }
    
    /**
     * エンドポイントを取得する。
     *
     * @return string
     */
    protected function getEndPoint():string {
        return  'api/arrival';
    }

    protected function verifyCard($stock_id, array $json) {
        $this->verifyCardFromParent($stock_id, $json);
        $exp_stock = Stockpile::where(GCon::ID, $stock_id)->first();
        $this->assertEquals($exp_stock->language, $json[Header::LANG], '言語');
        $this->assertEquals($exp_stock->condition, $json[Header::CONDITION], '状態');
    }
}
