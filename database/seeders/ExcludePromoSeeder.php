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
        // ExcludePromo::create(['attr' => 'invisibleink', 'name' => '不可視インク仕様']);
        // ExcludePromo::create(['attr' => 'serialized', 'name' => 'シリアル番号付き']);
        // ExcludePromo::create(['attr' => 'rainbow', 'name' => 'レイズド・フォイル']);
        ExcludePromo::create(['attr' => 'vault', 'name' => '「宝物庫」フレーム']);
    }
}
