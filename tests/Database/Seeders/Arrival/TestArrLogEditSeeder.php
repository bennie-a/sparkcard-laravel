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
        $logs = collect([
            '入荷情報編集カード_出荷情報なし',
            '入荷情報編集カード_Notionカードなし',
            '入荷情報編集カード_削除後在庫数0',
        ])->map(function ($name) {
            return $this->makeLogEntry($name);
        })->all();
    
        ArrivalLog::factory()->createMany($logs);
    }

    private function makeLogEntry(string $productName): array
    {
        $stock = Stockpile::find($productName, "XLN", "NM", "JP", false);

        if (!$stock) {
            throw new \RuntimeException("Stockpile not found for: {$productName}");
        }

        return [
            'stock_id' => $stock->id,
            Acon::ARRIVAL_DATE => TestDateUtil::formatToday(),
            StockpileHeader::QUANTITY => 2,
            SCon::VENDOR_TYPE_ID => 1,
        ];
    }
}