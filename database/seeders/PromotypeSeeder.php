<?php

namespace Database\Seeders;

use App\Models\Expansion;
use App\Models\Promotype;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PromotypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('promotype')->truncate();
        
        $exp_id = 'exp_id';
        
        // 共通
        $com = Expansion::findBySetCode('COM');
        // WAR
        $war = Expansion::findBySetCode('WAR');
        // NEO
        $neo = Expansion::findBySetCode('NEO');
        // MOM
        $mom = Expansion::findBySetCode('MOM');
        // AER
        $aer = Expansion::findBySetCode('AER');
        // ELD
        $eld = Expansion::findBySetCode('ELD');
        // STX
        $stx = Expansion::findBySetCode('STX');      
        // SCH(Store Championship)
        $sch = Expansion::findBySetCode('SCH');
        // WOE
        $woe = Expansion::findBySetCode('WOE');
        
        // ONE
        $one = Expansion::findBySetCode('ONE');
        // MKM
        $mkm = Expansion::findBySetCode('MKM');
        // MH3
        $mh3 = Expansion::findBySetCode('MH3');
        // DSK
        $dsk = Expansion::findBySetCode('DSK');
        // DFT
        $dft = Expansion::findBySetCode('DFT');
        $items = [
            ['attr' => 'draft', 'name' => '', $exp_id => $com->notion_id],
            ['attr' => 'showcase', 'name' => 'ショーケース', $exp_id => $com->notion_id],
            ['attr' => 'buyabox', 'name' => 'BOXプロモ特典', $exp_id => $com->notion_id],
            ['attr' => 'boosterfun', 'name' => 'ブースターファン', $exp_id => $com->notion_id],
            ['attr' => 'fullart', 'name' => 'フルアート', $exp_id => $com->notion_id],
            ['attr' => 'boxtopper', 'name' => 'ボックストッパー', $exp_id => $com->notion_id],
            ['attr' => 'borderless', 'name' => 'ボーダーレス', $exp_id => $com->notion_id],
            ['attr' => 'bundle', 'name' => 'バンドル', $exp_id => $com->notion_id],
            ['attr' => 'tourney', 'name' => 'トーナメント景品', $exp_id => $com->notion_id],
            ['attr' => 'stamped', 'name' => 'プレリリース', $exp_id => $com->notion_id],
            ['attr' => 'jpwalker', 'name' => '絵違い', $exp_id => $war->notion_id],
            ['attr' => 'neonink', 'name' => 'ネオンインク', $exp_id => $neo->notion_id],
            ['attr' => 'halofoil', 'name' => 'ハロー・Foil', $exp_id => $mom->notion_id],
            ['attr' => 'intropack', 'name' => 'エントリーセット', $exp_id => $aer->notion_id],
            ['attr' => 'brawldeck', 'name' => 'ブロールデッキ', $exp_id => $eld->notion_id],
            ['attr' => 'jpainting', 'name' => '日本画ミスティカルアーカイブ', $exp_id => $stx->notion_id],
            ['attr' => 'textless', 'name' => 'テキストレス・フルアート', $exp_id => $sch->notion_id],
            ['attr' => 'bringafriend', 'name' => 'Bring-a-Friend', $exp_id => $sch->notion_id],
            ['attr' => 'adventure', 'name' => 'おとぎ話', $exp_id => $woe->notion_id],
            ['attr' => 'anime', 'name' => 'アニメ・ボーダーレス', $exp_id => $woe->notion_id],
            ['attr' => 'phoilslick', 'name' => 'ファイレクシア語「胆液」', $exp_id => $one->notion_id],
            ['attr' => 'oilslickshowcase', 'name' => 'ショーケース「胆液」', $exp_id => $one->notion_id],
            ['attr' => 'oilslick', 'name' => 'ボーダレス「胆液」ショーケース', $exp_id => $one->notion_id],
            ['attr' => 'stepandcompleat', 'name' => 'S&C', $exp_id => $one->notion_id],
            ['attr' => 'concept', 'name' => 'コンセプトアート', $exp_id => $one->notion_id],
            ['attr' => 'magnified', 'name' => '「拡大鏡」ショーケース', $exp_id => $mkm->notion_id],
            ['attr' => 'dossier', 'name' => '「事件簿」ショーケース', $exp_id => $mkm->notion_id],
            ['attr' => 'ravnicacity', 'name' => '大都市ラヴニカ', $exp_id => $mkm->notion_id],
            ['attr' => 'profiles', 'name' => '「プロファイル」ボーダーレス', $exp_id => $mkm->notion_id],
            ['attr' => 'flamebreak', 'name' => '「フレームブレイク」ボーダーレス', $exp_id => $mh3->notion_id],
            ['attr' => 'oldframe', 'name' => '旧枠', $exp_id => $mh3->notion_id],
            ['attr' => 'doubleexposure', 'name' => '「二重露光」ショーケース', $exp_id => $dsk->notion_id],
            ['attr' => 'paranormal', 'name' => '「超常」フレーム', $exp_id => $dsk->notion_id],
            ['attr' => 'mirror', 'name' => '「鏡の怪物」ボーダーレス', $exp_id => $dsk->notion_id],
            ['attr' => 'maxspeed', 'name' => '「最大出力」ボーダーレス', $exp_id => $dft->notion_id],
            ['attr' => 'badassrider', 'name' => '「ワルなライダー」ボーダーレス', $exp_id => $dft->notion_id],
            ['attr' => 'graffgiant', 'name' => '「グラフィティ・ジャイアント」ボーダーレス', $exp_id => $dft->notion_id],
            ['attr' => 'firstplacefoil', 'name' => 'ファーストプレイス・Foil', $exp_id => $dft->notion_id]
        ]; 

        foreach($items as $i ){
            Promotype::create($i);
        }
    }
}
