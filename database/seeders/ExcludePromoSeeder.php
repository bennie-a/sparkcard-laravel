<?php

namespace Database\Seeders;

use App\Models\ExcludePromo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExcludePromoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('exclude_promo')->truncate();
        ExcludePromo::create(['attr' => 'invisibleink', 'name' => '不可視インク仕様']);
        ExcludePromo::create(['attr' => 'serialized', 'name' => 'シリアル番号付き']);
        ExcludePromo::create(['attr' => 'rainbow', 'name' => 'レイズド・フォイル']);
        ExcludePromo::create(['attr' => 'vault', 'name' => '「宝物庫」フレーム']);
        ExcludePromo::create(['attr' => 'ripplefoil', 'name' => 'リップル・フォイル']);
        ExcludePromo::create(['attr' => 'promopack', 'name' => 'イベント用プロモパック']);
        ExcludePromo::create(['attr' => 'starterdeck', 'name' => 'スターターキット']);
        ExcludePromo::create(['attr' => 'fracturefoil', 'name' => 'フラクチャー・Foil']);
        ExcludePromo::create(['attr' => 'textured', 'name' => 'テクスチャー・Foil']);
    }
}
