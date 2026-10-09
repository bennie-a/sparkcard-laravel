<?php

namespace Tests\Database\Seeders\CLI\Divide;

use App\Models\CardInfo;
use App\Models\Expansion;
use App\Models\Promotype;
use App\Models\ShippingLog;
use App\Models\Stockpile;
use App\Services\Constant\CardConstant;
use App\Services\Constant\GlobalConstant;
use Illuminate\Database\Seeder;

/**
 * DivideOrderTestクラス用のテストデータ
 */
class DivideShippingLogSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CardInfo::factory(20)->create();
        Stockpile::factory(20)->create();
        ShippingLog::factory(20)->create();
    }
}
