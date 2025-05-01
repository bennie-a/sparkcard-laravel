<?php

namespace Tests\Database\Seeders\Arrival;

use App\Models\ArrivalLog;
use App\Models\Stockpile;
use Illuminate\Database\Seeder;
use Tests\Util\TestDateUtil;
use App\Services\Constant\ArrivalConstant as ACon;
use App\Services\Constant\SearchConstant as SCon;
use App\Services\Constant\StockpileHeader;

class TestArrLogEditSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $stock_id = 'stock_id';
        $noshipt = Stockpile::find("入荷情報編集カード_出荷情報なし", "XLN", "NM", "JP", false);
        TestDateUtil::formatYesterday();
        $logs = [];
        $logs[] = [
            $stock_id => $noshipt->id,
            Acon::ARRIVAL_DATE => TestDateUtil::formatToday(),
            StockpileHeader::QUANTITY => 2,
            SCon::VENDOR_TYPE_ID => 1,            
        ];
        ArrivalLog::factory()->createMany($logs);
    }
}