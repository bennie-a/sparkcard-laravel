<?php

namespace Tests\Unit\DB\Arrival;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Services\Constant\ArrivalConstant as ACon;
use App\Services\Constant\CardConstant;
use App\Services\Constant\GlobalConstant;
use App\Services\Constant\StockpileHeader as Header;
use App\Services\Constant\SearchConstant as SCon;
use Illuminate\Http\Response;
use Tests\Database\Seeders\Arrival\TestArrivalLogSeeder;
use Tests\Database\Seeders\DatabaseSeeder;
use Tests\Database\Seeders\TestCardInfoSeeder;
use Tests\Database\Seeders\TestStockpileSeeder;
use Tests\Database\Seeders\TruncateAllTables;

class ArrivalLogUpdateTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(TruncateAllTables::class);
        $this->seed(DatabaseSeeder::class);
        $this->seed(TestCardInfoSeeder::class);
        $this->seed(TestStockpileSeeder::class);
        $this->seed(TestArrivalLogSeeder::class);
    }

    /**
     *入荷情報の枚数と入荷カテゴリ以外の各値が更新されているか確認するテスト
     * @dataProvider updateValueProvider
     */
    public function test_update_ok(array $data): void
    {
        $id = 1; // 更新する入荷情報のID
        $response = $this->update(1, $data);
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
        $response->assertJsonFragment($data);

        $data[GlobalConstant::ID] = $id;
        $this->assertDatabaseHas('arrival_log', $data);        
    }

    public function updateValueProvider(): array
    {
        return [
            '原価' => [[Header::COST => fake()->randomNumber(3)]],
            // '入荷日' => [[ACon::ARRIVAL_DATE => fake()->dateTimeBetween('-7 days', '-4 days')->format('Y/m/d')]],
        ];
    }

    public function test_入荷カテゴリ(int $id, int $vendor_type_id, String $vendor): void
    {
        $data = [SCon::VENDOR_TYPE_ID => $vendor_type_id, ACon::VENDOR => $vendor];
        $response = $this->update($id, $data);
        $response->assertJsonFragment($data);

        $data[GlobalConstant::ID] = $id;
        $this->assertDatabaseHas('arrival_log', $data);

    }

    private function update(int $id, array $data) {
        $response = $this->put("/api/arrival/{$id}", $data);
        $response->assertOk();
        return $response;
    }

}
