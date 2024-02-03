<?php

namespace Database\Seeders;

use App\Models\ExcludePromo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExcludePromoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ExcludePromo::create(['attr' => 'invisibleink', 'name' => '不可視インク仕様']);
    }
}
