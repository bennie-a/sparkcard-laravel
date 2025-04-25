<?php

namespace Tests\Database\Seeders\Arrival;

use App\Models\ArrivalLog;
use App\Models\Stockpile;
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
        $current_date =TestDateUtil::formatToday();
        $yesterday = TestDateUtil::formatYesterday();
        $two_days_before = TestDateUtil::formatTwoDateBefore();
        $three_days_before = TestDateUtil::formatThreeDateBefore();
        $arrival_dates  = [$current_date, $yesterday, $two_days_before, $three_days_before];

        $all_stock = Stockpile::all()->toArray();
        // 買取カテゴリ
        $buy_items = array_map(function($s) {
            return ['stock_id' => $s['id'],
                         SCon::VENDOR_TYPE_ID => 3, 'vendor' => fake()->unique()->word()];
        }, $all_stock);
        
        foreach(range(1, 5) as $i) {
            if ($i == 3) {
                continue;
            }
            $items = array_map(function($s) use($i) {
                return ['stock_id' => $s['id'],
                             SCon::VENDOR_TYPE_ID => $i];
            }, $all_stock);
            $buy_items = array_merge($buy_items, $items);
        }

        foreach($arrival_dates as $d) {
            $logs = array_map(function($item) use ($d) {
                $item[ACon::ARRIVAL_DATE] = $d;
                return $item;
            }, $buy_items);
            ArrivalLog::factory()->createMany($logs);
        }
    }
}
