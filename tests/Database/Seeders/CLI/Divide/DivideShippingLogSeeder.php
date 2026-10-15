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
use Illuminate\Support\Str;
use InvalidArgumentException;
use Tests\Unit\DB\Shipt\ShiptLogTestHelper;

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

        $before260930 = Carbon::parse(fake()->dateTimeBetween('-3 years', '2026-09-30'));
        $after261001 = Carbon::parse(fake()->dateTimeBetween('2026-10-01', 'now'));
        // メルカリ経由ミニレター発送
        $this->createMiniRecord(ShopPlatform::MERCARI, [300, 1499]);
        // BASE経由ミニレター発送
        $this->createMiniRecord(ShopPlatform::BASE, [200, 4999] );

        // // メルカリ経由クリックポスト発送
        $this->createClickRecord(ShopPlatform::MERCARI, [1500, 9999]);
        // // メルカリ経由簡易書留発送
        $this->createClickRecord(ShopPlatform::MERCARI, [10000]);
        // // BASE経由クリックポスト発送
        $this->createClickRecord(ShopPlatform::BASE, [5000, 10000]);

        // // 直接取引
        $this->createClickRecord(ShopPlatform::DIRECT, [5000]);
    }

    private function createMiniRecord(ShopPlatform $platform, array $prices) {
        $before240930 = Carbon::parse(fake()->dateTimeBetween('-3 years', '2024-09-30'));
        $after241001 = Carbon::parse(fake()->dateTimeBetween('2024-10-01', 'now'));
        $this->generateRandomRecord($platform, [$before240930, $after241001], $prices);
    }

    private function createClickRecord(ShopPlatform $platform, array $prices) {
        $before260930 = Carbon::parse(fake()->dateTimeBetween('-3 years', '2026-09-30'));
        $after261001 = Carbon::parse(fake()->dateTimeBetween('2026-10-01', 'now'));
        $this->generateRandomRecord($platform, [$before260930, $after261001], $prices);
    }

    /**
     * 指定した金額をランダムで割り振って、注文番号と購入者情報
     * が同じレコードをランダムな件数で登録する。
     *
     * @param ShopPlatform $platform
     * @param array $initialDate 起点日
     * @param array $prices
     * @return void
     */
    private function generateRandomRecord(ShopPlatform $platform, array $initialDate, array $prices) {
        foreach ($initialDate as $date) {
            foreach($prices as $p) {
                $orderId = $this->generateOrderId($platform);
                $itemCount = fake()->numberBetween(1, 2);
                $splitPrices = $this->splitNumberRandomly($p, $itemCount);
                $buyerInfo = ShiptLogTestHelper::createBuyerInfoOnly();
                foreach($splitPrices as $sp) {
                    ShippingLog::factory()->createRecord($platform, $date, $sp, $orderId, $itemCount, $buyerInfo)->create();
                }
            }
        }
    }

    // 各条件を満たしたデータを1件ずつ作成する。
    // 灯争大戦ブースターパック[JP]
    // 直接取引用レコード
    // らくらくメルカリ便
    // 1購入者複数商品注文
    // 直接取引

    /**
     * プラットフォームに応じて注文番号をランダムに生成する。
     *
     * @return string
     */
    private function generateOrderId(ShopPlatform $platform): string
    {
        if ($platform === ShopPlatform::MERCARI) {
            return 'order_'.Str::random(22);
        }
        if ($platform === ShopPlatform::DIRECT) {
            return 'd2c_'.Str::random(22);
        }
        return Str::upper(Str::random(16));
    }

    /**
     * 数値をランダムに分割する（整数版）
     *
     * @param int $total 分割したい合計値
     * @param int $count 分割する個数
     * @return array
     */
    function splitNumberRandomly(int $total, int $count, int $minPrice = 100): array
    {
        // 安全チェック：全商品に最低金額を配っても合計金額を超えないか確認
        if ($total < ($count * $minPrice)) {
            throw new InvalidArgumentException(
                "金額が不足しています。商品数 {$count} 個で最低金額 {$minPrice} 円の場合、合計は最低でも " . ($count * $minPrice) . " 円以上にする必要があります。"
            );
        }

        // 1. まず全員に最低金額（$minPrice）を等しく配る
        $prices = array_fill(0, $count, $minPrice);

        // 2. 残りの金額を計算する（例: 300円 - 3点×1円 = 残り297円）
        $remainingAmount = $total - ($count * $minPrice);

        // 残り金額がある場合のみ、ランダムに分配する
        if ($remainingAmount > 0) {
            // 残り金額を切り分けるための境界点をランダムに選ぶ（0 〜 残り金額 の間から抽出）
            $points = collect(range(0, $remainingAmount))
                ->random($count - 1)
                ->sort()
                ->values();

            $prev = 0;
            foreach ($points as $index => $point) {
                // 分配された差分金額を、最初に配った最低金額に上乗せする
                $prices[$index] += ($point - $prev);
                $prev = $point;
            }
            // 最後の残りを上乗せ
            $prices[$count - 1] += ($remainingAmount - $prev);
        }

        // 3. 配列をシャッフルして、特定の位置だけ金額が高くなる偏りを防ぐ
        shuffle($prices);
        return $prices;
    }
}
