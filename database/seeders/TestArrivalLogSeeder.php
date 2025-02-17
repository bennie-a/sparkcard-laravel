<?php

namespace Database\Seeders;

use App\Models\ArrivalLog;
use App\Models\VendorType;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
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
        // vendor_type_id=3、入荷日が現在日時を3件
        // vendor_type_id=3、入荷日が1日前を3件
        // vendor_type_id=3、入荷日が2日前を3件
        // vendor_type_id=3、入荷日が3日前を3件
        // vendor_type_id=3以外の数字、入荷日が現在日時を3件ずつ
        // vendor_type_id=3以外の数字、入荷日が2日前を3件ずつ
        // vendor_type_id=3以外の数字、入荷日が3日前を3件ずつ
        $current_date =CarbonImmutable::now();
        $yesterday = Carbon::yesterday();
        $two_days_before = $current_date->subDays(2);
        $three_days_before = $current_date->subDays(3);
        $arrival_dates  = [$current_date, $yesterday, $two_days_before, $three_days_before];
        $numbers = range(1, 5);
        $vendor_type_ids = array_diff($numbers, [3]);
        logger()->debug($current_date);
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
