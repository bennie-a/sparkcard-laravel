<?php

namespace Tests\Database\Seeders\CLI\Divide;

use App\Models\CardInfo;
use App\Models\Expansion;
use App\Models\Promotype;
use App\Models\ShippingLog;
use App\Models\Stockpile;
use App\Services\Constant\CardConstant;
use App\Services\Constant\GlobalConstant;
use Illuminate\Database\Seeder;

/**
 * DivideOrderTestクラス用のテストデータ
 */
class DivideShippingLogSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CardInfo::factory(20)->create();
        Stockpile::factory(20)->create();
        ShippingLog::factory(20)->create();
    }

    // 各条件を満たしたデータを2件ずつ作成する。
    // 灯争大戦ブースターパック[JP]
    // BASE用レコード
    // メルカリShops用レコード
    // 直接取引用レコード
    // らくらくメルカリ便
    // 簡易書留
    // 1購入者複数商品注文
    // 発送日が2024/9/30以前で、メルカリShops経由でのミニレター注文
    // 発送日が2024/9/30以前で、BASEショップ経由でのミニレター注文
    // 発送日が2024/10/1以降で、メルカリShops経由でのミニレター注文
    // 発送日が2024/10/1以降で、BASEショップ経由でのミニレター注文
    // 発送日が2026/9/30以前で、メルカリShops経由でのクリックポスト注文
    // 発送日が2026/9/30以前で、BASEショップ経由でのクリックポスト注文
    // 発送日が2026/10/1以降で、メルカリShops経由でのクリックポスト注文
    // 発送日が2026/10/1以降で、BASEショップ経由でのクリックポスト注文
    // 直接取引
}
