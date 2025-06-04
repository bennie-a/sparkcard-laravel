<?php

namespace Database\Seeders;

use App\Enum\VendorTypeCat;
use App\Models\VendorType;
use App\Services\Constant\CardConstant as Con;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VendorTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('vendor_type')->delete();
        DB::statement('ALTER SEQUENCE vendor_type_id_seq RESTART WITH 1');
        $categories = VendorTypeCat::toTextArray();
        foreach ($categories as $category) {
            VendorType::create([Con::NAME => $category]);
        }
    }
}
