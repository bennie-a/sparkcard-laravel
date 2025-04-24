<?php

namespace Database\Seeders;

use App\Models\CardInfo;
use App\Models\Stockpile;
use Illuminate\Database\Seeder;
use App\Services\Constant\CardConstant as Con;
use App\Services\Constant\StockpileHeader as Header;

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
        
        $pre_delete = CardInfo::findSingleCard('XLN', '削除テストカード_出荷情報あり', false);
        Stockpile::create(['card_id' => $pre_delete->id, Header::CONDITION => 'NM', 
                                                                                                Con::QUANTITY => 3, Header::LANGUAGE => 'JP']);
 
        $cardIds = range(4, 12);
        $cardIds = array_diff($cardIds, array(9, 10));
        $cardIds = array_values(($cardIds));
        $stocks = array_map(function($c) {
            return ['card_id' => $c, 'condition' => 'NM', 'language' => 'JP'];
        }, $cardIds);

        Stockpile::factory()->createMany($stocks);
    }
}
