<?php
namespace Tests\Database\Seeders\Arrival;

use App\Models\CardInfo;
use App\Models\Stockpile;
use Tests\Database\Seeders\TestStockpileSeeder;
use App\Services\Constant\CardConstant as Con;
use App\Services\Constant\StockpileHeader as Header;

class TestArrivalStockpileSeeder extends TestStockpileSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pre_delete = CardInfo::findSingleCard('XLN', '入荷情報編集カード_出荷情報あり', false);
        Stockpile::create([Header::CARD_ID => $pre_delete->id, Header::CONDITION => 'NM', 
                                                                                                Con::QUANTITY => 3, Header::LANGUAGE => 'JP']);

        $no_shipt_log = CardInfo::findSingleCard('XLN', '入荷情報編集カード_削除後在庫数1以上', false);
        Stockpile::create([Header::CARD_ID => $no_shipt_log->id, Header::CONDITION => 'NM', 
                                                                                                Con::QUANTITY => 5, Header::LANGUAGE => 'JP']);
       
        $no_notion = CardInfo::findSingleCard('XLN', '入荷情報編集カード_Notionカードなし', false);
        Stockpile::create([Header::CARD_ID => $no_notion->id, Header::CONDITION => 'NM', 
                                                                                                Con::QUANTITY => 7, Header::LANGUAGE => 'JP']);

        $nostock = CardInfo::findSingleCard('XLN', '入荷情報編集カード_削除後在庫数0', false);
        Stockpile::create([Header::CARD_ID => $nostock->id, Header::CONDITION => 'NM', 
                                                                                                Con::QUANTITY => 2, Header::LANGUAGE => 'JP']);
        
        $minus_stock = CardInfo::findSingleCard('XLN', '入荷情報編集カード_削除後在庫数-1', false);
        Stockpile::create([Header::CARD_ID => $minus_stock->id, Header::CONDITION => 'NM', 
                                                                                                Con::QUANTITY => 1, Header::LANGUAGE => 'JP']);
                                                                                        
    }
}
