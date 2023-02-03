<?php

namespace Database\Seeders;

use App\Models\FrameEffects;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FrameEffectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        FrameEffects::create(['attr' => 'fullart', 'name' => 'フルアート']);
        FrameEffects::create(['attr' => 'fandfc', 'name' => '両面カード']);
    }
}
