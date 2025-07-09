
<?php

namespace Tests\Database\Seeders\Arrival;

use App\Models\CardInfo;
use App\Models\Stockpile;
use Illuminate\Support\Facades\DB;
use App\Services\Constant\StockpileHeader as Header;

use Illuminate\Database\Seeder;

/**
 * ArrivalLogPostTest.phpで使用するためのSeederクラス
 */
class PostStockpileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $draft = CardInfo::findSingleCard('BRO', 'ファイレクシアのドラゴン・エンジン', false);
        Stockpile::create([Header::CARD_ID => $draft->id, 'condition' => 'NM', 'quantity' => 1, 'language' => 'JP']);
    }
}