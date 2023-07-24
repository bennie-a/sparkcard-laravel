<?php

namespace Database\Seeders;

use App\Facades\ExService;
use App\Models\CardInfo;
use App\Models\Expansion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestCardInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 兄弟戦争
        CardInfo::create(['exp_id' => 'd4d9832d-3bb3-4e07-be26-6a37ec198991', 'name' => 'ドラゴンの運命',
        'en_name' => 'Draconic Destiny', 'color_id' => 'R', 'number' => '130',
         'isFoil' => false, 'image_url' => '', 'barcode' => 'xxxxxxxx']);
        CardInfo::create(['exp_id' => 'd4d9832d-3bb3-4e07-be26-6a37ec198991', 'name' => 'ファイレクシアのドラゴン・エンジン',
        'en_name' => 'Phyrexian Dragon Engine', 'color_id' => 'A', 'number' => '163',
         'isFoil' => false, 'image_url' => '', 'barcode' => 'xxxxxxxy']);
         CardInfo::create(['exp_id' => 'd4d9832d-3bb3-4e07-be26-6a37ec198991', 'name' => 'ファイレクシアのドラゴン・エンジン',
         'en_name' => 'Phyrexian Dragon Engine', 'color_id' => 'A', 'number' => '163',
          'isFoil' => true, 'image_url' => '', 'barcode' => 'xxxxxxxz']);
      }
}
