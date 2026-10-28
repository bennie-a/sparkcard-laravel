<?php

namespace Tests\Database\Seeders\CLI\Divide;

use App\Enum\CardLanguage;
use App\Enum\ShiptMethod;
use App\Enum\ShopPlatform;
use App\Models\CardInfo;
use App\Models\Expansion;
use App\Models\Shipping;
use App\Models\ShippingLog;
use App\Models\Stockpile;
use App\Services\Constant\CardConstant;
use App\Services\Constant\GlobalConstant;
use App\Services\Constant\StockpileHeader;
use App\Services\Constant\ShiptConstant as SC;
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

        // メルカリ経由¥300の商品が1件➞ミニレター
        $this->createUnder1500Record(ShopPlatform::MERCARI, 300, 1);
        // メルカリ経由¥300の商品が2件➞ミニレター
        $this->createUnder1500Record(ShopPlatform::MERCARI, 300, 2);
        // メルカリ経由¥1499の商品が1件➞ミニレター
        $this->createUnder1500Record(ShopPlatform::MERCARI, 1499, 1);
        // メルカリ経由¥1499の商品が2件➞ミニレター
        $this->createUnder1500Record(ShopPlatform::MERCARI, 1499, 2);

        // BASE経由¥113の商品が1件➞ミニレター
        $this->createUnder1500Record(ShopPlatform::BASE, 135, 1 );
        // BASE経由¥4999の商品が1件➞ミニレター
        $this->createUnder1500Record(ShopPlatform::BASE, 4999, 1 );
        // BASE経由¥4999の商品が2件➞ミニレター
        $this->createUnder1500Record(ShopPlatform::BASE, 4999, 2 );

        // // メルカリ経由クリックポスト発送
        // $this->createOver1500Record(ShopPlatform::MERCARI, [1500, 9999]);
        // // BASE経由クリックポスト発送
        // $this->createOver1500Record(ShopPlatform::BASE, [5000, 10000]);

        // // メルカリ経由簡易書留発送
        // $this->createRegisterMailRecord(ShopPlatform::MERCARI, 10000);
        // // 直接取引
        // $this->createOver1500Record(ShopPlatform::DIRECT, [5000]);

        // らくらくメルカリ便
        // ShippingLog::factory()->create(['order_id' => $this->generateOrderId(ShopPlatform::MERCARI),
        //                                         SC::ZIPCODE => '100-0000', SC::ADDRESS => '匿名希望', 'total_price' => 2000]);

        // 灯争大戦ブースターパック[JP]
    //     $war = Expansion::findBySetCode('WAR');
    //     $pack = CardInfo::factory()->create([CardConstant::EXP_ID => $war->notion_id,
    //                                 GlobalConstant::NAME => '灯争大戦ブースターパック', CardConstant::PROMO_ID => 1]);
    //     $packStock = Stockpile::factory()->create([
    //         StockpileHeader::CARD_ID => $pack->id,
    //         StockpileHeader::LANGUAGE => CardLanguage::JP->value,
    //         StockpileHeader::CONDITION => 'NM'
    //     ]);

    //      $shiptInfo = [
    //                 [
    //                     'order_id' => $this->generateOrderId(ShopPlatform::MERCARI),
    //                     SC::STOCK_ID => $packStock->id,
    //                     StockpileHeader::QUANTITY => 1,
    //                     'single_price' => 795,
    //                     'total_price' => 795
    //                 ],
    //                 [
    //                     'order_id' => $this->generateOrderId(ShopPlatform::MERCARI),
    //                     SC::STOCK_ID => $packStock->id,
    //                     StockpileHeader::QUANTITY => 30,
    //                     'single_price' => 799,
    //                     'total_price' => 23995
    //                 ]
    //             ];
    //             ShippingLog::factory()->createMany($shiptInfo);
    }

    private function createUnder1500Record(ShopPlatform $platform, int $price, int $itemCount) {
        $before240930 = Carbon::parse(fake()->dateTimeBetween('-3 years', '2024-09-30'));
        $after241001 = Carbon::parse(fake()->dateTimeBetween('2024-10-01', 'now'));
        $this->generateShiptRecord($platform, [$before240930, $after241001], $price, $itemCount);
    }

    private function createOver1500Record(ShopPlatform $platform, array $prices) {
        $before260930 = Carbon::parse(fake()->dateTimeBetween('-3 years', '2026-09-30'));
        $after261001 = Carbon::parse(fake()->dateTimeBetween('2026-10-01', 'now'));
        $this->generateRandomRecord($platform, [$before260930, $after261001], $prices);
    }

    private function createRegisterMailRecord(ShopPlatform $platform, int $price) {
        $shiptDate = Carbon::parse(fake()->dateTimeBetween('now'));
        $this->generateRandomRecord($platform, [$shiptDate], [$price], 300);
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
    private function generateRandomRecord(ShopPlatform $platform, array $initialDate, array $prices, int $minPrice = 100) {
        foreach ($initialDate as $date) {
            foreach($prices as $p) {
                $orderId = $this->generateOrderId($platform);
                $itemCount = 2;
                $splitPrices = $this->splitNumberRandomly($p, $itemCount, $minPrice);
                $buyerInfo = ShiptLogTestHelper::createBuyerInfoOnly();
                $fee = 0;
                if ($platform === ShopPlatform::MERCARI) {
                    $fee = $this->getMercariFee($date, $p);
                } else if ($platform === ShopPlatform::BASE) {
                    $fee = $this->getBaseFee($date, $p);
                }
                foreach($splitPrices as $sp) {
                    ShippingLog::factory()->createRecord($fee, $date, $sp, $orderId, $itemCount, $buyerInfo)->create();
                }
            }
        }
    }

        /**
     * 指定した金額をランダムで割り振って、注文番号と購入者情報
     * が同じレコードを登録する。
     *
     * @param ShopPlatform $platform
     * @param array $initialDate 起点日
     * @param int $price
     * @param int $itemCount
     * @return void
     */
    private function generateShiptRecord(ShopPlatform $platform, array $initialDate, int $price, int $itemCount) {
        foreach ($initialDate as $date) {
            $orderId = $this->generateOrderId($platform);
            $splitPrices = $itemCount == 1 ? [$price] : $this->splitNumberRandomly($price, $itemCount, 100);
            $buyerInfo = ShiptLogTestHelper::createBuyerInfoOnly();
            $fee = 0;
            if ($platform === ShopPlatform::MERCARI) {
                $fee = $this->getMercariFee($date, $price);
            } else if ($platform === ShopPlatform::BASE) {
                $fee = $this->getBaseFee($date, $price);
            }
            foreach($splitPrices as $sp) {
                ShippingLog::factory()->createRecord($fee, $date, $sp, $orderId, $itemCount, $buyerInfo)->create();
            }
        }
    }

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

    private function getMercariFee(Carbon $shiptDate, int $subtotal) {
        $fee = 0;
        if ($subtotal >= 300 && $subtotal < 1500) {
            $fee = $this->fetchMiniletter($shiptDate);
        } else if ($subtotal >= 1500 && $subtotal < 10000) {
            $fee = $this->fetchClickPost($shiptDate);
        } else if ($subtotal >= 10000) {
            $fee = $this->fetchRegisterMail();
        }
        return $fee;
    }

    private function getBaseFee(Carbon $shiptDate, int $subtotal) {
        $fee = 0;
        if ($subtotal >= 135 && $subtotal < 4999) {
            $fee = $this->fetchMiniletter($shiptDate);
        } else {
            $fee = $this->fetchClickPost($shiptDate);
        }
        return $fee;
    }

    /**
     * ミニレターの価格を取得する。
     *
     * @param Carbon $shiptDate
     * @return integer
     */
    private function fetchMiniletter(Carbon $shiptDate): int
    {
        $initialDate = Carbon::parse('2024-09-30');
        $isBefore = $shiptDate->lte($initialDate);
        return $this->fetchShiptPrice($isBefore, ShiptMethod::MINI);
    }

    /**
     * クリックポストの価格を取得する。
     *
     * @param Carbon $shiptDate
     * @return integer
     */
    private function fetchClickPost(Carbon $shiptDate): int
    {
        $initialDate = Carbon::parse('2026-09-30');
        $isBefore = $shiptDate->lte($initialDate);
        return $this->fetchShiptPrice($isBefore, ShiptMethod::CLICK);
    }

    /**
     * 簡易書留の価格を取得する。
     *
     * @return integer
     */
    private function fetchRegisterMail(): int
    {
        return $this->fetchShiptPrice(false, ShiptMethod::REGISTER_MAIL);
    }

    /**
     * 送料を取得する。
     *
     * @param boolean $deleted
     * @param ShiptMethod $method
     * @return integer
     */
    private function fetchShiptPrice(bool $deleted, ShiptMethod $method): int
    {
        $shipping = Shipping::where(GlobalConstant::NAME, $method->value)
                                ->where('deleted', $deleted)->first();
        return $shipping->price ?? 0;
    }
}
