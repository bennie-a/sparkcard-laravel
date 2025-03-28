<?php

namespace Database\Seeders;

use App\Models\ArrivalLog;
use App\Models\VendorType;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Services\Constant\ArrivalConstant as ACon;
use App\Services\Constant\SearchConstant as SCon;
use Tests\Util\TestDateUtil;

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
        $current_date =TestDateUtil::today();
        $yesterday = Carbon::yesterday();
        $two_days_before = $current_date->subDays(2);
        $three_days_before = $current_date->subDays(3);
        $arrival_dates  = [$current_date, $yesterday, $two_days_before, $three_days_before];
        $numbers = range(1, 5);
        $vendor_type_ids = array_diff($numbers, [3]);

        // 告別≪ショーケース≫
        ArrivalLog::factory()
            ->createOne([ACon::ARRIVAL_DATE => $current_date, 'stock_id' => 7, SCon::VENDOR_TYPE_ID => 1]);
        // 機械の母、エリシュ・ノーン≪ボーダレス「胆液」≫
        ArrivalLog::factory()
            ->createOne([ACon::ARRIVAL_DATE => $current_date, 'stock_id' => 10, SCon::VENDOR_TYPE_ID => 1]);
        foreach($arrival_dates as $d) {
            ArrivalLog::factory()->count(3)->create([
                'vendor_type_id'=> 3,
                'vendor'=> fake()->unique()->word(),
                'arrival_date' => $d
            ]);
    
            foreach($vendor_type_ids as $i) {
                ArrivalLog::factory()->count(3)->create([
                    'vendor_type_id'=> $i,
                    'arrival_date' => $d
                ]);
                }
        }
    }
}
