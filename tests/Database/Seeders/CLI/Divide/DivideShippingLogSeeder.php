<?php

namespace Tests\Database\Seeders\CLI\Divide;

use App\Enum\ShopPlatform;
use App\Models\CardInfo;
use App\Models\Expansion;
use App\Models\Promotype;
use App\Models\ShippingLog;
use App\Models\Stockpile;
use App\Services\Constant\CardConstant;
use App\Services\Constant\GlobalConstant;
use App\Services\Constant\StockpileHeader;
use Carbon\Carbon;
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
        CardInfo::factory(40)->create()->each(function (CardInfo $card) {
            Stockpile::factory()->create([
                StockpileHeader::CARD_ID => $card->id,
            ]);
        });

        $before2024 = Carbon::parse(fake()->dateTimeBetween('-3 years', '2024-09-30'));
        ShippingLog::factory()->createMercariRecord($before2024, 300)->create();
        ShippingLog::factory()->createMercariRecord($before2024, 1499)->create();
        ShippingLog::factory()->createMercariRecord($before2024, 1500)->create();
        ShippingLog::factory()->createMercariRecord($before2024, 9999)->create();
        ShippingLog::factory()->createMercariRecord($before2024, 10000)->create();
    }

    // 各条件を満たしたデータを1件ずつ作成する。
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
