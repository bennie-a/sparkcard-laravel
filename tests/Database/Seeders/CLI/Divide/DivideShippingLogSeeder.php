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

        $before240930 = Carbon::parse(fake()->dateTimeBetween('-3 years', '2024-09-30'));
        $after241001 = Carbon::parse(fake()->dateTimeBetween('2024-10-01', 'now'));
        $before260930 = Carbon::parse(fake()->dateTimeBetween('-3 years', '2026-09-30'));
        $after261001 = Carbon::parse(fake()->dateTimeBetween('2026-10-01', 'now'));

        foreach ([$before240930, $after241001] as $date) {
            foreach([300, 1499] as $p) {
                ShippingLog::factory()->createRecord(ShopPlatform::MERCARI, $date, $p)->create();
            }
        }

        foreach ([$before260930, $after261001] as $date) {
            foreach([1500, 9999] as $p) {
                ShippingLog::factory()->createRecord(ShopPlatform::MERCARI, $date, $p)->create();
            }
        }
        ShippingLog::factory()->createRecord(ShopPlatform::MERCARI, $before240930, 10000)->create();

        foreach ([$before240930, $after241001] as $date) {
            foreach([185, 4999] as $p) {
                ShippingLog::factory()->createRecord(ShopPlatform::BASE, $date, $p)->create();
            }
        }

        foreach ([$before260930, $after261001] as $date) {
            foreach([5000, 10000] as $p) {
                ShippingLog::factory()->createRecord(ShopPlatform::BASE, $date, $p)->create();
            }
        }

    }

    // 各条件を満たしたデータを1件ずつ作成する。
    // 灯争大戦ブースターパック[JP]
    // BASE用レコード
    // メルカリShops用レコード
    // 直接取引用レコード
    // らくらくメルカリ便
    // 1購入者複数商品注文
    // 直接取引
}
