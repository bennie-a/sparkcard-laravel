<?php

namespace Database\Seeders;

use App\Models\ArrivalLog;
use App\Models\VendorType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * テスト用入荷ログデータ作成クラス
 */
class TestArrivalLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ArrivalLog::factory()->count(10)->create([
            'vendor_type_id'=> 3,
            'vendor'=> fake()->word(),
        ]);

        for($i = 1; $i <= 5; $i++) {
            if ($i == 3) {
                continue;
            }
            ArrivalLog::factory()->count(10)->create([
                'vendor_type_id'=> $i,
            ]);
        }
    }
}
