<?php

namespace Tests\Database\Seeders;

use App\Facades\ExService;
use App\Models\CardInfo;
use App\Models\Expansion;
use App\Models\Foiltype;
use Illuminate\Database\Seeder;
use App\Services\Constant\CardConstant as CCon;

class TestCardInfoSeeder extends Seeder
{
    
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $nonfoil = Foiltype::findByAttr('nonfoil');
        $foil = Foiltype::findByAttr('foil');
        // S&C・Foil
        $scfoil = Foiltype::findByAttr('stepandcompleat');

        // 兄弟戦争
        $bro = $this->findNotionId('BRO');
        
        CardInfo::factory()->createOne([CCon::EXP_ID => $bro, 'name' => 'ドラゴンの運命',
        CCon::EN_NAME => 'Draconic Destiny', 'color_id' => 'R', CCon::NUMBER => '130',
        CCon::IS_FOIL => false, 'image_url' => '', 'barcode' => 'xxxxxxxx', 'foiltype_id' => $nonfoil->id]);
        CardInfo::factory()->createOne([CCon::EXP_ID => $bro, 'name' => 'ファイレクシアのドラゴン・エンジン',
        CCon::EN_NAME => 'Phyrexian Dragon Engine', 'color_id' => 'A', CCon::NUMBER => '163',
        CCon::IS_FOIL => false, 'image_url' => '', 'barcode' => 'xxxxxxxy', 'foiltype_id' => $nonfoil->id]);
        CardInfo::factory()->createOne([CCon::EXP_ID => $bro, 'name' => 'ファイレクシアのドラゴン・エンジン',
        CCon::EN_NAME => 'Phyrexian Dragon Engine', 'color_id' => 'A', CCon::NUMBER => '163',
        CCon::IS_FOIL => true, 'image_url' => '', 'foiltype_id' => $foil->id]);

        // 灯争大戦
        $war = $this->findNotionId('WAR');
        CardInfo::factory()->createOne([CCon::EXP_ID => $war, 'name' => '群れの声、アーリン≪絵違い≫',
        CCon::EN_NAME => 'Arlinn, Voice of the Pack', 'color_id' => 'G', CCon::NUMBER => '150',
         CCon::IS_FOIL => true, 'image_url' => '', 'foiltype_id' => $foil->id]);

         // 神河：輝ける世界
         $neo = $this->findNotionId('NEO');
        CardInfo::factory()->createOne([CCon::EXP_ID => $neo, 'name' => '発展の暴君、ジン＝ギタクシアス',
        CCon::EN_NAME => 'Jin-Gitaxias, Progress Tyrant', 'color_id' => 'U', CCon::NUMBER => '59',
        CCon::IS_FOIL => false, 'image_url' => '', 'foiltype_id' => $nonfoil->id]);
        CardInfo::factory()->createOne([CCon::EXP_ID => $neo, 'name' => '発展の暴君、ジン＝ギタクシアス',
        CCon::EN_NAME => 'Jin-Gitaxias, Progress Tyrant', 'color_id' => 'U', CCon::NUMBER => '59',
        CCon::IS_FOIL => true, 'image_url' => '', 'foiltype_id' => $foil->id]);
        CardInfo::factory()->createOne([CCon::EXP_ID => $neo, 'name' => '告別≪ショーケース≫',
    CCon::EN_NAME => 'Farewell', 'color_id' => 'W', CCon::NUMBER => '365',
        CCon::IS_FOIL => true, 'image_url' => '', 'foiltype_id' => $foil->id]);

        // ファイレクシア：完全なる統一
        $one = $this->findNotionId('ONE');
        CardInfo::factory()->createOne([CCon::EXP_ID => $one, 'name' => '機械の母、エリシュ・ノーン≪ボーダレス「胆液」≫',
        CCon::EN_NAME => 'Elesh Norn, Mother of Machines', 'color_id' => 'W', CCon::NUMBER => '298',
        CCon::IS_FOIL => true, 'foiltype_id' => $scfoil->id]);
        CardInfo::factory()->createOne([CCon::EXP_ID => $one, 'name' => '完成化した精神、ジェイス',
        CCon::EN_NAME => 'Jace, the Perfected Mind', 'color_id' => 'U', CCon::NUMBER => '57',
        CCon::IS_FOIL => false, 'foiltype_id' => $nonfoil->id]);
        CardInfo::factory()->createOne([CCon::EXP_ID => $one, 'name' => 'ドロスの魔神',
        CCon::EN_NAME => 'Archfiend of the Dross', 'color_id' => 'B', CCon::NUMBER => '82',
        CCon::IS_FOIL => false, 'foiltype_id' => $nonfoil->id]);

        // イクサラン
        $xln = $this->getXlnNotionId();
        CardInfo::factory()->createOne([CCon::EXP_ID => $xln, 'name' => '軍団の上陸',
        CCon::EN_NAME => 'Legion\'s Landing  Adanto', 'color_id' => 'W', CCon::NUMBER => '22',
        CCon::IS_FOIL => false, 'foiltype_id' => $nonfoil->id]);
        CardInfo::factory()->createOne([CCon::EXP_ID => $xln, 'name' => '軍団の上陸≪プレリリース≫',
        CCon::EN_NAME => 'Legion\'s Landing  Adanto', 'color_id' => 'W', CCon::NUMBER => '22s',
        CCon::IS_FOIL => true, 'foiltype_id' => $foil->id]);

        CardInfo::factory()->createOne([CCon::EXP_ID => $xln, 'name' => '在庫情報なし',
        CCon::EN_NAME => 'No Info', 'color_id' => 'W', CCon::NUMBER => '1',
        CCon::IS_FOIL => false, 'foiltype_id' => $nonfoil->id]);
    }
        
    protected function findNotionId(string $attr) : string {
        $set = Expansion::findBySetCode($attr);
        return $set->notion_id;
    }

    protected function getXlnNotionId() : string {
        return $this->findNotionId('XLN');
    }

    protected function getNonFoil() :Foiltype {
        return Foiltype::findByAttr('nonfoil');
    }
}
