<?php

namespace Tests\Unit\DB\Arrival;

use App\Enum\VendorTypeCat;
use App\Models\ArrivalLog;
use App\Models\Stockpile;
use App\Models\VendorType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Services\Constant\ArrivalConstant as ACon;
use App\Services\Constant\CardConstant;
use App\Services\Constant\GlobalConstant;
use App\Services\Constant\StockpileHeader as Header;
use App\Services\Constant\SearchConstant as SCon;
use App\Services\Constant\StockpileHeader;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Response;
use Illuminate\Testing\TestResponse;
use Tests\Database\Seeders\Arrival\TestArrivalStockpileSeeder;
use Tests\Database\Seeders\Arrival\TestArrLogCardInfoSeeder;
use Tests\Database\Seeders\Arrival\TestArrLogEditSeeder;
use Tests\Database\Seeders\DatabaseSeeder;
use Tests\Database\Seeders\TestCardInfoSeeder;
use Tests\Database\Seeders\TestStockpileSeeder;
use Tests\Database\Seeders\TruncateAllTables;
use Tests\Trait\GetApiAssertions;
use Tests\Util\TestDateUtil;

class ArrivalLogUpdateTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(TruncateAllTables::class);
        $this->seed(DatabaseSeeder::class);
        $this->seed(TestArrLogCardInfoSeeder::class);
        $this->seed(TestArrivalStockpileSeeder::class);
        $this->seed(TestArrLogEditSeeder::class);
    }

    use GetApiAssertions;

    /**
     *入荷情報の枚数と入荷カテゴリ以外の各値が更新されているか確認するテスト
     * @dataProvider updateValueProvider
     */
    public function test_update_ok(array $data): void
    {
        $id = 1; // 更新する入荷情報のID
        $response = $this->update_ok(1, $data);
        $response->assertJsonFragment($data);

        $data[GlobalConstant::ID] = $id;
        $this->assertDatabaseHas('arrival_log', $data);        
    }

    public function updateValueProvider(): array
    {
        return [
            '原価' => [[Header::COST => fake()->randomNumber(3)]],
            '入荷日_今日以前の日付' => [[ACon::ARRIVAL_DATE => fake()->dateTimeBetween('-7 days', '-4 days')->format('Y/m/d')]],
            '入荷日_今日' => [[ACon::ARRIVAL_DATE => TestDateUtil::formatToday()]],
        ];
    }

    /**
     * 入荷カテゴリの更新テスト
     * @dataProvider vendorTypeProvider
     */
    public function test_入荷カテゴリ(int $id, VendorTypeCat $cat, ?string $vendor, callable $method): void
    {
        $data = [SCon::VENDOR_TYPE_ID => $cat->value, ACon::VENDOR => $vendor];
        $response = $this->update_ok($id, $data);

        // JSONの検証
        $json = $response->json();
        $this->assertArrayHasKey(ACon::VENDOR, $json, 'vendor要素が含まれない');
        $method($json[ACon::VENDOR]);

        $data[GlobalConstant::ID] = $id;
        $actual = ArrivalLog::where($data)->first();
        $this->assertNotNull($actual, '入荷情報が更新されていない');
    }

    protected function vendorTypeProvider(): array
    {
        return [
            'オリジナルパック' => [5, VendorTypeCat::ORIGINAL_PACK, null, $this->verifyOtherVendor()],
            '私物' => [1, VendorTypeCat::PERSONAL, null, $this->verifyOtherVendor()],
            '買取' => [1, VendorTypeCat::PURCHASE, fake()->name, $this->verifyBuyVendor()],
            '棚卸し' => [1, VendorTypeCat::INVENTORY, null, $this->verifyOtherVendor()],
            '返品' => [1, VendorTypeCat::RETURNED, null, $this->verifyOtherVendor()],
        ];
    }

    /**
     * 入荷枚数の更新テスト
     * @dataProvider qtyProvider
     */
    public function test_入荷枚数(int $arrival_id, int $expected_qty): void
    {
        $data[Header::QUANTITY] = 4;
        $response = $this->update_ok($arrival_id, $data);

        // JSONの検証
        $json = $response->json();
        $this->assertArrayHasKey(Header::QUANTITY, $json, '入荷枚数が含まれない');
        $this->assertEquals($data[Header::QUANTITY] , $json[Header::QUANTITY], '入荷枚数が更新されていない');

        // 入荷ログテーブルの検証
        $data[GlobalConstant::ID] = $arrival_id;
        $this->assertDatabaseHas(ArrivalLog::class, $data);

        // 在庫数の検証
        $log = ArrivalLog::findWithStockInfo($arrival_id);
        $stock = Stockpile::findById($log->stock_id);
        $this->assertEquals($expected_qty, $stock->quantity, '在庫数が更新されていない');
    }

    protected function qtyProvider()
    {
        return [
            '変更前の入荷枚数 = 在庫数' => [3, 4],
            '変更前の入荷枚数 < 在庫数' => [2, 9],
            '変更前の入荷枚数 > 在庫数' => [4, 4],
        ];
    }

    /**
     * 変更処理に成功した場合を検証する。
     *
     * @param integer $id 入荷情報ID
     * @param array $data 更新データ
     * @return \Illuminate\Testing\TestResponse
     */
    private function update_ok(int $id, array $data) {
        $response = $this->execute($id, $data);
        $response->assertOk();
        $response->assertJsonStructure(
            [
                GlobalConstant::ID,
                Header::COST,
                Header::QUANTITY,
                ACon::ARRIVAL_DATE,
                GlobalConstant::CARD => [
                    GlobalConstant::ID,
                    Header::NAME,
                    CardConstant::EXP => [
                        CardConstant::ATTR,
                        CardConstant::NAME
                    ],
                    Header::LANG,
                    Header::CONDITION,
                    Header::FOIL => [
                        'is_foil',
                        CardConstant::NAME,
                    ]
                ],
                ACon::VENDOR => [
                    GlobalConstant::ID,
                    Header::NAME,
                    ACon::SUPPLIER
                ]
            ]);
        return $response;
    }
    public function test_ng_変更対象が存在しない() {
        $response = $this->execute(99999, [Header::QUANTITY => 1]);
        $response->assertStatus(HttpResponse::HTTP_NOT_FOUND);
        $response->assertJson([
            'title' => '情報なし',
            'detail' => '指定した情報がありません。',
            'status' => HttpResponse::HTTP_NOT_FOUND,
            'request' => 'api/arrival/99999',
        ]);
    }

    /**
     * バリデーションエラーを検証する。
     * @dataProvider validationNgProvider
     * @return void
     */
    public function test_validation_ng(array $data, string $msg): void{
        $id = 1; // 更新する入荷情報のID
        $response = $this->execute($id, $data);
        $response->assertStatus(HttpResponse::HTTP_BAD_REQUEST);
        $response->assertJson([
            'title' => 'Validation Error',
            'detail' => $msg,
            'status' => HttpResponse::HTTP_BAD_REQUEST,
            'request' => 'api/arrival/1',
        ]);
    }

    protected function validationNgProvider(): array
    {
        return [
            'NG 原価が負の値' => [[Header::COST => -1], '原価は1以上の数字を入力してください。'],            
            'NG 原価が0' => [[Header::COST => 0], '原価は1以上の数字を入力してください。'], 
            'NG 入荷枚数が負の値' => [[Header::QUANTITY => -1], '入荷枚数は1以上の数字を入力してください。'],
            'NG 入荷枚数が0' => [[Header::QUANTITY => 0], '入荷枚数は1以上の数字を入力してください。'],
            'NG 入荷日が未来の日付' => [[ACon::ARRIVAL_DATE => now()->addDays(1)->format('Y/m/d')], '入荷日は今日以前の日付を入力してください。'],
            'NG 入荷日が日付ではない' => [[ACon::ARRIVAL_DATE => 'invalid-date'], '入荷日が日付形式ではありません。'],
            'NG 入荷カテゴリが存在しない' => [[SCon::VENDOR_TYPE_ID => 999], '取引先カテゴリIDは1～5の間の数字を設定してください。'],
            'NG 入荷カテゴリが「買取」以外で、取引先が存在する' => [
                        [SCon::VENDOR_TYPE_ID => VendorTypeCat::ORIGINAL_PACK->value, ACon::VENDOR => fake()->name],
                         '取引先カテゴリIDが「買取」以外の時は取引先は入力しないでください。'],
            'NG 入荷カテゴリが買取で、取引先が存在しない' => [
                [SCon::VENDOR_TYPE_ID => VendorTypeCat::PURCHASE->value], 
                '取引先カテゴリIDが「買取」の時は取引先は必ず入力してください。'],
        ];
    }
    
    /**
     * 変更処理を実行する。
     *
     * @param integer $id
     * @param array $data
     * @return TestResponse
     */
    private function execute(int $id, array $data) {
        return $this->put("/api/arrival/{$id}", $data);
    }

}
