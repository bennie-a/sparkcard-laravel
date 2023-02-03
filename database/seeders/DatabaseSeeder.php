<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Enum\PromoType;
use App\Models\BaseCategory;
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
        $this->call(MainColorSeeder::class);
        $this->call(ShippingSeeder::class);
        $this->call(BaseCategory::class);
        $this->call(PromotypeSeeder::class);
        $this->call(FrameEffectsSeeder::class);
    }
}
