<?php

namespace Database\Seeders;

use App\Models\MainColor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MainColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MainColor::create(['attr' => "B",  'name' => "黒"]);
        MainColor::create(['attr' => "G", 'name' => "緑"]);
        MainColor::create(['attr' => "R", 'name' => "赤"]);
        MainColor::create(['attr' => "U", 'name' => "青"]);
        MainColor::create(['attr' => "W", 'name' => "白"]);
        MainColor::create(['attr' => "M", 'name' => "多色"]);
        MainColor::create(['attr' => "Land", 'name' => "土地"]);
        MainColor::create(['attr' => "A", 'name' => "アーティファクト"]);
        MainColor::create(['attr' => "L", 'name' => "無色"]);
        //
    }
}
