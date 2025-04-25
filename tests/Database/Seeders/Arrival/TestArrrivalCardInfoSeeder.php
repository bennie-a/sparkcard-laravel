<?php

use App\Models\CardInfo;
use Illuminate\Database\Seeder;
use Tests\Database\Seeders\TestCardInfoSeeder;
use App\Services\Constant\CardConstant as CCon;

/**
 * 入荷情報の編集・削除用のカード情報を作成するSeederクラス
 */
class TestArrrivalCardInfoSeeder extends TestCardInfoSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $nonfoil = $this->getNonFoil();
        $xln = $this->getXlnNotionId();

        $items = [];
        $items[] =[CCon::EXP_ID => $xln, 'name' => '入荷情報編集カード_出荷情報あり',
        CCon::EN_NAME => 'Pre-Delete Card', 'color_id' => 'W', CCon::NUMBER => '2',
        CCon::IS_FOIL => false, 'foiltype_id' => $nonfoil->id];

        $items[] = [CCon::EXP_ID => $xln, 'name' => '入荷情報編集カード_出荷情報なし',
        CCon::EN_NAME => 'Pre-Delete Card_No_Shipt_Info', 'color_id' => 'W', CCon::NUMBER => '3',
        CCon::IS_FOIL => false, 'foiltype_id' => $nonfoil->id];

        $items[] = [CCon::EXP_ID => $xln, 'name' => '入荷情報編集カード_Notionカードなし',
        CCon::EN_NAME => 'Pre-Delete Card', 'color_id' => 'W', CCon::NUMBER => '4',
        CCon::IS_FOIL => false, 'foiltype_id' => $nonfoil->id];

        CardInfo::factory()->createMany($items);
    }
}
