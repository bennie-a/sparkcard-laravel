<?php

namespace Tests\Database\Seeders;

use Database\Seeders\CsvHeaderSeeder;
use Database\Seeders\ExcludePromoSeeder;
use Database\Seeders\ExpansionSeeder;
use Database\Seeders\FoiltypeSeeder;
use Database\Seeders\MainColorSeeder;
use Database\Seeders\PromotypeSeeder;
use Database\Seeders\ShippingSeeder;
use Database\Seeders\VendorTypeSeeder;
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
        $this->call(ExpansionSeeder::class);
        $this->call(TestExpansionSeeder::class);
        $this->call(MainColorSeeder::class);
        $this->call(ShippingSeeder::class);
        $this->call(PromotypeSeeder::class);
        $this->call(FoiltypeSeeder::class);
        $this->call(CsvHeaderSeeder::class);
        $this->call(ExcludePromoSeeder::class);
        $this->call(VendorTypeSeeder::class);
        logger()->info('マスタデータ登録終了');
    }
}
