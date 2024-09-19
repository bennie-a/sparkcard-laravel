<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Enum\PromoType;
use App\Models\BaseCategory;
use App\Models\CsvHeader;
use App\Models\Shipping;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        logger()->info('マスタデータ登録開始');
        $this->call(MainColorSeeder::class);
        $this->call(ShippingSeeder::class);
        $this->call(PromotypeSeeder::class);
        $this->call(FoiltypeSeeder::class);
        $this->call(CsvHeaderSeeder::class);
        $this->call(ExcludePromoSeeder::class);
        logger()->info('マスタデータ登録終了');
    }
}
