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

        $dross = CardInfo::findSingleCard('ONE', 'ドロスの魔神', false);
        Stockpile::create(['card_id' => $dross->id, 'condition' => 'NM', 'quantity' => 1, 'language' => 'JP']);

        $jace = CardInfo::findSingleCard('ONE', '完成化した精神、ジェイス', false);
        Stockpile::create(['card_id' => $jace->id, 'condition' => 'NM-', 'quantity' => 2, 'language' => 'JP']);
 
        for($i = 4; $i <= 12; $i++) {
            if ($i == 9) {
                continue;
            }
            Stockpile::factory()->create([
                'card_id'=> $i,
            ]);
        }
    }
}
