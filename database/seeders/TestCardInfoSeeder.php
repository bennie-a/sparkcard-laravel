<?php

namespace Database\Seeders;

use App\Facades\ExService;
use App\Models\CardInfo;
use App\Models\Expansion;
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
        // 兄弟戦争
        $bro = $this->findNotionId('BRO');
        
        CardInfo::factory()->createOne(['exp_id' => $bro, 'name' => 'ドラゴンの運命',
        'en_name' => 'Draconic Destiny', 'color_id' => 'R', 'number' => '130',
        'isFoil' => false, 'image_url' => '', 'barcode' => 'xxxxxxxx']);
        CardInfo::factory()->createOne(['exp_id' => $bro, 'name' => 'ファイレクシアのドラゴン・エンジン',
        'en_name' => 'Phyrexian Dragon Engine', 'color_id' => 'A', 'number' => '163',
        'isFoil' => false, 'image_url' => '', 'barcode' => 'xxxxxxxy']);
        CardInfo::factory()->createOne(['exp_id' => $bro, 'name' => 'ファイレクシアのドラゴン・エンジン',
        'en_name' => 'Phyrexian Dragon Engine', 'color_id' => 'A', 'number' => '163',
        'isFoil' => true, 'image_url' => '']);

        // 灯争大戦
        $war = $this->findNotionId('WAR');
        CardInfo::factory()->createOne(['exp_id' => $war, 'name' => '群れの声、アーリン≪絵違い≫',
        'en_name' => 'Arlinn, Voice of the Pack', 'color_id' => 'G', 'number' => '150',
         'isFoil' => true, 'image_url' => '']);
    }
        
        private function findNotionId(string $attr) : string {
            $set = Expansion::where('attr', 'BRO')->first();
            return $set->notion_id;
      }
}
