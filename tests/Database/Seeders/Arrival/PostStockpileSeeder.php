<?php
namespace Tests\Database\Seeders\Arrival;

use App\Models\CardInfo;
use App\Models\Stockpile;
use Tests\Database\Seeders\TestStockpileSeeder;
use App\Services\Constant\CardConstant as Con;
use App\Services\Constant\StockpileHeader as Header;
use Illuminate\Database\Seeder;

class PostStockpileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $draft = CardInfo::findSingleCard('NEO', '発展の暴君、ジン＝ギタクシアス', false);
        Stockpile::create([Header::CARD_ID => $draft->id, 'condition' => 'NM', 'quantity' => 1, 'language' => 'JP']);

        $norn = CardInfo::findSingleCard('ONE', '機械の母、エリシュ・ノーン', true);
        Stockpile::create([Header::CARD_ID => $norn->id, 'condition' => 'NM-', 'quantity' => 0, 'language' => 'EN']);

    }
}
