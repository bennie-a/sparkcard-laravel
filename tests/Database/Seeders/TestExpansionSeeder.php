<?php

namespace Tests\Database\Seeders;

use App\Models\Expansion;
use App\Models\Stockpile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestExpansionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $items = [];
        $items[] = ['attr' => '4ED', 'name' => '第4版'];
        $items[] = ['attr' => 'BRO', 'name' => '兄弟戦争'];
        $items[] = ['attr' => 'WAR', 'name' => '灯争大戦'];
        $items[] = ['attr' => 'NEO', 'name' => '神河：輝ける世界'];
        $items[] = ['attr' => 'ONE', 'name' => 'ファイレクシア：完全なる統一', 'notion_id' => '1a36d935-ee60-466b-ac43-ecc797c0ee87'];
        $items[] = ['attr' => 'DMU', 'name' => '団結のドミナリア'];
        $items[] = ['attr' => 'MIR', 'name' => 'ミラージュ'];
        $items[] = ['attr' => 'MUL', 'name' => '機械兵団の進軍_多元宇宙の伝説'];
        $items[] = ['attr' => 'MOM', 'name' => '機械兵団の進軍'];
        $items[] = ['attr' => 'WOE', 'name' => 'エルドレインの森'];
        $items[] = ['attr' => 'WOT', 'name' => 'エルドレインの森おとぎ話カード'];
        $items[] = ['attr' => 'SCH', 'name' => 'Store Championships'];
        $items[] = ['attr' => 'XLN', 'name' => 'イクサラン', 'notion_id' => '5ede77f3a80542d3a4259e28b6f069dd'];
        $items[] = ['attr' => 'LCI', 'name' => 'イクサラン:失われし洞窟'];
        $items[] = ['attr' => 'MKM', 'name' => 'カルロフ邸殺人事件'];
        $items[] = ['attr' => 'SPG', 'name' => 'スペシャルゲスト'];
        $items[] = ['attr' => 'MH1', 'name' => 'モダンホライゾン'];
        $items[] = ['attr' => 'MH3', 'name' => 'モダンホライゾン3'];
        $items[] = ['attr' => 'DSK', 'name' => 'ダスクモーン：戦慄の館'];
        $items[] = ['attr' => 'DFT', 'name' => '霊気走破'];
        $items[] = ['attr' => 'TDM', 'name' => 'タルキール:龍嵐録'];
        $items[] = ['attr' => 'AER', 'name' => '霊気紛争'];
        $items[] = ['attr' => 'ELD', 'name' => 'エルドレインの王権'];
        $items[] = ['attr' => 'STX', 'name' => 'ストリクスヘイヴン：魔法学院'];
        $items[] = ['attr' => 'SCH', 'name' => 'Store Championship'];
        $items[] = ['attr' => 'BLB', 'name' => 'ブルームバロウ'];

        Expansion::factory()->createMany($items);
    }
}
