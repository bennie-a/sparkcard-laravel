<?php

namespace Database\Seeders;

use App\Facades\ExService;
use App\Models\CardInfo;
use App\Models\Expansion;
use App\Models\Foiltype;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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

        // 兄弟戦争
        $bro = $this->findNotionId('BRO');
        
        CardInfo::factory()->createOne(['exp_id' => $bro, 'name' => 'ドラゴンの運命',
        'en_name' => 'Draconic Destiny', 'color_id' => 'R', 'number' => '130',
        'isFoil' => false, 'image_url' => '', 'barcode' => 'xxxxxxxx', 'foiltype_id' => $nonfoil->id]);
        CardInfo::factory()->createOne(['exp_id' => $bro, 'name' => 'ファイレクシアのドラゴン・エンジン',
        'en_name' => 'Phyrexian Dragon Engine', 'color_id' => 'A', 'number' => '163',
        'isFoil' => false, 'image_url' => '', 'barcode' => 'xxxxxxxy', 'foiltype_id' => $nonfoil->id]);
        CardInfo::factory()->createOne(['exp_id' => $bro, 'name' => 'ファイレクシアのドラゴン・エンジン',
        'en_name' => 'Phyrexian Dragon Engine', 'color_id' => 'A', 'number' => '163',
        'isFoil' => true, 'image_url' => '', 'foiltype_id' => $foil->id]);

        // 灯争大戦
        $war = $this->findNotionId('WAR');
        CardInfo::factory()->createOne(['exp_id' => $war, 'name' => '群れの声、アーリン≪絵違い≫',
        'en_name' => 'Arlinn, Voice of the Pack', 'color_id' => 'G', 'number' => '150',
         'isFoil' => true, 'image_url' => '', 'foiltype_id' => $foil->id]);

         // 神河：輝ける世界
         $neo = $this->findNotionId('NEO');
        CardInfo::factory()->createOne(['exp_id' => $neo, 'name' => '発展の暴君、ジン＝ギタクシアス',
        'en_name' => 'Jin-Gitaxias, Progress Tyrant', 'color_id' => 'U', 'number' => '59',
        'isFoil' => false, 'image_url' => '', 'foiltype_id' => $nonfoil->id]);
        CardInfo::factory()->createOne(['exp_id' => $neo, 'name' => '発展の暴君、ジン＝ギタクシアス',
        'en_name' => 'Jin-Gitaxias, Progress Tyrant', 'color_id' => 'U', 'number' => '59',
        'isFoil' => true, 'image_url' => '', 'foiltype_id' => $foil->id]);
        CardInfo::factory()->createOne(['exp_id' => $neo, 'name' => '告別≪ショーケース≫',
    'en_name' => 'Farewell', 'color_id' => 'W', 'number' => '365',
        'isFoil' => true, 'image_url' => '', 'foiltype_id' => $foil->id]);

        // ファイレクシア：完全なる統一
        $one = $this->findNotionId('ONE');
        CardInfo::factory()->createOne(['exp_id' => $one, 'name' => '機械の母、エリシュ・ノーン≪ボーダレス「胆液」≫',
        'en_name' => 'Elesh Norn, Mother of Machines', 'color_id' => 'W', 'number' => '298',
        'isFoil' => false, 'foiltype_id' => $nonfoil->id]);
        CardInfo::factory()->createOne(['exp_id' => $one, 'name' => '完成化した精神、ジェイス',
        'en_name' => 'Jace, the Perfected Mind', 'color_id' => 'U', 'number' => '57',
        'isFoil' => false, 'foiltype_id' => $nonfoil->id]);
        CardInfo::factory()->createOne(['exp_id' => $one, 'name' => 'ドロスの魔神',
        'en_name' => 'Archfiend of the Dross', 'color_id' => 'B', 'number' => '82',
        'isFoil' => false, 'foiltype_id' => $nonfoil->id]);
    }
        
        private function findNotionId(string $attr) : string {
            $set = Expansion::where('attr', $attr)->first();
            return $set->notion_id;
      }
}
