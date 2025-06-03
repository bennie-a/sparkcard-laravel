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
use Tests\Database\Seeders\Arrival\TestArrivalStockpileSeeder;
use Tests\Database\Seeders\Arrival\TestArrLogCardInfoSeeder;
use Tests\Database\Seeders\Arrival\TestArrLogEditSeeder;
use Tests\Database\Seeders\DatabaseSeeder;
use Tests\Database\Seeders\TestCardInfoSeeder;
use Tests\Database\Seeders\TestStockpileSeeder;
use Tests\Database\Seeders\TruncateAllTables;
use Tests\Trait\GetApiAssertions;

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
            '入荷日' => [[ACon::ARRIVAL_DATE => fake()->dateTimeBetween('-7 days', '-4 days')->format('Y/m/d')]],
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

    // NG_変更対象が存在しない
    // NG costが負の値
    // NG costが0
    // NG 入荷枚数が負の値
    // NG 入荷枚数が0
    // NG 入荷日が未来の日付
    // NG 入荷日が日付ではない
    // NG 入荷カテゴリが存在しない
    // NG 入荷カテゴリが買取以外で、取引先が存在する
    // NG 入荷カテゴリが買取で、取引先が存在しない

    protected function qtyProvider()
    {
        return [
            '変更前の入荷枚数 = 在庫数' => [3, 4],
            '変更前の入荷枚数 < 在庫数' => [2, 9],
            '変更前の入荷枚数 > 在庫数' => [4, 4],
        ];
    }

    private function update_ok(int $id, array $data) {
        $response = $this->put("/api/arrival/{$id}", $data);
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

}
