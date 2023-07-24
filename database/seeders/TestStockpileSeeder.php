<?php

namespace Database\Seeders;

use App\Models\CardInfo;
use App\Models\Stockpile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestStockpileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $draft = CardInfo::findSingleCard('BRO', 'ファイレクシアのドラゴン・エンジン', false);
        Stockpile::create(['card_id' => $draft->id, 'condition' => 'NM', 'quantity' => 1, 'language' => 'JP']);

        $foil = CardInfo::findSingleCard('BRO', 'ファイレクシアのドラゴン・エンジン', true);
        Stockpile::create(['card_id' => $foil->id, 'condition' => 'NM', 'quantity' => 3, 'language' => 'JP']);

        $zero = CardInfo::findSingleCard('BRO', 'ドラゴンの運命', false);
        Stockpile::create(['card_id' => $zero->id, 'condition' => 'NM', 'quantity' => 0, 'language' => 'JP']);
    }
}
