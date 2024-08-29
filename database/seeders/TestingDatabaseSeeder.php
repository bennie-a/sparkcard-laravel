<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestingDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            // TruncateAllTables::class,
            TestExpansionSeeder::class,
            MainColorSeeder::class,
            ShippingSeeder::class,
            FoiltypeSeeder::class,
            TestCardInfoSeeder::class,
            TestStockpileSeeder::class
        ]);
        //
    }
}
