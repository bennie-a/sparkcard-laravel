<?php

namespace Database\Seeders;

use App\Models\Promotype;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PromotypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Promotype::create(['attr' => 'jpwalker', 'name' => '絵違い']);
        Promotype::create(['attr' => 'boosterfun', 'name' => 'ブースターファン']);
        Promotype::create(['attr' => 'draft', 'name' => '通常版']);
        Promotype::create(['attr' => 'buyabox', 'name' => 'BOXプロモ特典']);
        Promotype::create(['attr' => 'textured', 'name' => 'テクスチャー']);
        Promotype::create(['attr' => 'bundle', 'name' => 'バンドル']);
        Promotype::create(['attr' => 'promopack', 'name' => 'プロモカード']);
        Promotype::create(['attr' => 'showcase', 'name' => 'ショーケース']);
        Promotype::create(['attr' => 'extendedart', 'name' => '拡張アート']);
        Promotype::create(['attr' => 'neonink', 'name' => 'ネオンインク']);
        Promotype::create(['attr' => 'themepack', 'name' => 'テーマブースター限定']);
        Promotype::create(['attr' => 'oilslick', 'name' => 'ボーダレス「胆液」ショーケース']);
        Promotype::create(['attr' => 'concept', 'name' => 'コンセプトアート']);
        Promotype::create(['attr' => 'stepandcompleat', 'name' => 'S&C']);
        
    }
}