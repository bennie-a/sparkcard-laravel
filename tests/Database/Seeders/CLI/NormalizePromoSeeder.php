<?php

namespace Tests\Database\Seeders\CLI;

use App\Models\Expansion;
use App\Models\Promotype;
use App\Services\Constant\CardConstant as CCon;
use App\Services\Constant\GlobalConstant as GCon;
use Illuminate\Database\Seeder;

class NormalizePromoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 共通
        $com = Expansion::findBySetCode('COM');
        $mkm = Expansion::findBySetCode('MKM');
        $items = [
            [CCon::ATTR => 'draft', 'name' => '', CCon::EXP_ID => $com->notion_id],
            [CCon::ATTR => 'magnified', 'name' => '「拡大鏡」ショーケース', CCon::EXP_ID => $mkm->notion_id],
            [CCon::ATTR => 'dossier', 'name' => '「事件簿」ショーケース', CCon::EXP_ID => $mkm->notion_id],
            [CCon::ATTR => 'ravnicacity', 'name' => '大都市ラヴニカ', CCon::EXP_ID => $mkm->notion_id],
            [CCon::ATTR => 'profiles', 'name' => '「プロファイル」ボーダーレス', CCon::EXP_ID => $mkm->notion_id],
        ];
        foreach($items as $i) {
            Promotype::create($i);
        }
    }
}
