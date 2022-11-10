<?php

namespace Database\Seeders;

use App\Models\BaseCategory;
use App\Models\MainColor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BaseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        BaseCategory::create([
            'exp_id'=>'d4d9832d-3bb3-4e07-be26-6a37ec198991',
            'color_id' => 'W',
            'base_id' => 4772193
        ]);
        BaseCategory::create([
            'exp_id'=>'d4d9832d-3bb3-4e07-be26-6a37ec198991',
            'color_id' => 'U',
            'base_id' => 4772194
        ]);
        BaseCategory::create([
            'exp_id'=>'d4d9832d-3bb3-4e07-be26-6a37ec198991',
            'color_id' => 'B',
            'base_id' => 4772195
        ]);
        BaseCategory::create([
            'exp_id'=>'d4d9832d-3bb3-4e07-be26-6a37ec198991',
            'color_id' => 'R',
            'base_id' => 4772196
        ]);
        BaseCategory::create([
            'exp_id'=>'d4d9832d-3bb3-4e07-be26-6a37ec198991',
            'color_id' => 'G',
            'base_id' => 4772197
        ]);
        BaseCategory::create([
            'exp_id'=>'d4d9832d-3bb3-4e07-be26-6a37ec198991',
            'color_id' => 'M',
            'base_id' => 4772198
        ]);
        BaseCategory::create([
            'exp_id'=>'d4d9832d-3bb3-4e07-be26-6a37ec198991',
            'color_id' => 'A',
            'base_id' => 4772199
        ]);
        BaseCategory::create([
            'exp_id'=>'d4d9832d-3bb3-4e07-be26-6a37ec198991',
            'color_id' => 'L',
            'base_id' => 4777609
        ]);
    }
}
