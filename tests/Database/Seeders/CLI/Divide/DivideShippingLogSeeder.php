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
        // メルカリ経由¥1498の商品が1件➞ミニレター
        $this->createUnder1500Record(ShopPlatform::MERCARI, 1498, 1);
        // メルカリ経由¥1498の商品が2件➞ミニレター
        $this->createUnder1500Record(ShopPlatform::MERCARI, 1498, 2);

        // BASE経由¥113の商品が1件➞ミニレター
        $this->generateShiptRecord(ShopPlatform::BASE,
                    [Carbon::parse(fake()->dateTimeBetween('-3 years', '2024-09-30'))], 113, 1);
                    // BASE経由¥135の商品が1件➞ミニレター
        $this->generateShiptRecord(ShopPlatform::BASE,
                    [Carbon::parse(fake()->dateTimeBetween('2024-10-01', 'now'))], 135, 1);
        // BASE経由¥4999の商品が1件➞ミニレター
        $this->createUnder1500Record(ShopPlatform::BASE, 4999, 1 );
        // BASE経由¥4999の商品が2件➞ミニレター
        $this->createUnder1500Record(ShopPlatform::BASE, 4999, 2 );

        // メルカリ経由¥1499の商品が1件➞クリックポスト
        $this->createOver1500Record(ShopPlatform::MERCARI, 1499, 1);
        // メルカリ経由¥1499の商品が2件➞クリックポスト
        $this->createOver1500Record(ShopPlatform::MERCARI, 1499, 2);
        // メルカリ経由¥9999の商品が1件➞クリックポスト
        $this->createOver1500Record(ShopPlatform::MERCARI, 9999, 1);
        // メルカリ経由¥9999の商品が2件➞クリックポスト
        $this->createOver1500Record(ShopPlatform::MERCARI, 9999, 2);
        // BASE経由¥5000の商品が1件➞クリックポスト
        $this->createOver1500Record(ShopPlatform::BASE, 5000, 1);
        // BASE経由¥5000の商品が2件➞クリックポスト
        $this->createOver1500Record(ShopPlatform::BASE, 5000, 2);
        // BASE経由¥10000の商品が1件➞クリックポスト
        $this->createOver1500Record(ShopPlatform::BASE, 10000, 1);
        // メルカリ経由¥10000の商品が2件➞クリックポスト
        $this->createOver1500Record(ShopPlatform::BASE, 10000, 2);

        // メルカリ経由簡易書留発送
        $this->createRegisterMailRecord(ShopPlatform::MERCARI, 11000, 1);
        // 直接取引
        $this->generateShiptRecord(ShopPlatform::DIRECT,
                         [Carbon::parse(fake()->dateTimeBetween('2024-10-01', 'now'))], 5000, 1);

        // らくらくメルカリ便
        ShippingLog::factory()->create(['order_id' => $this->generateOrderId(ShopPlatform::MERCARI),
                                                SC::ZIPCODE => '100-0000', SC::ADDRESS => '匿名希望', 'total_price' => 2000]);

        // 灯争大戦ブースターパック[JP]
        $war = Expansion::findBySetCode('WAR');
        $pack = CardInfo::factory()->create([CardConstant::EXP_ID => $war->notion_id,
                                    GlobalConstant::NAME => '灯争大戦ブースターパック', CardConstant::PROMO_ID => 1]);
        $packStock = Stockpile::factory()->create([
            StockpileHeader::CARD_ID => $pack->id,
            StockpileHeader::LANGUAGE => CardLanguage::JP->value,
            StockpileHeader::CONDITION => 'NM'
        ]);

         $shiptInfo = [
                    [
                        'order_id' => $this->generateOrderId(ShopPlatform::MERCARI),
                        SC::STOCK_ID => $packStock->id,
                        StockpileHeader::QUANTITY => 1,
                        'single_price' => 795,
                        'total_price' => 795,
                        'shipping_date' => Carbon::parse(fake()->dateTimeBetween('2023-12-16', '2024-01-20'))
                    ],
                    [
                        'order_id' => $this->generateOrderId(ShopPlatform::MERCARI),
                        SC::STOCK_ID => $packStock->id,
                        StockpileHeader::QUANTITY => 30,
                        'single_price' => 799,
                        'total_price' => 23995,
                        'shipping_date' => Carbon::parse(fake()->dateTimeBetween('2023-12-16', '2024-01-20'))
                    ],
                    [
                        'order_id' => $this->generateOrderId(ShopPlatform::BASE),
                        SC::STOCK_ID => $packStock->id,
                        StockpileHeader::QUANTITY => 1,
                        'single_price' => 795,
                        'total_price' => 795,
                        'shipping_date' => Carbon::parse(fake()->dateTimeBetween('2023-12-16', '2024-01-20'))
                    ],
                    [
                        'order_id' => $this->generateOrderId(ShopPlatform::BASE),
                        SC::STOCK_ID => $packStock->id,
                        StockpileHeader::QUANTITY => 30,
                        'single_price' => 799,
                        'total_price' => 23995,
                        'shipping_date' => Carbon::parse(fake()->dateTimeBetween('2023-12-16', '2024-01-20'))
                    ],
                ];
                ShippingLog::factory()->createMany($shiptInfo);
    }

    private function createUnder1500Record(ShopPlatform $platform, int $price, int $itemCount) {
        $before240930 = Carbon::parse(fake()->dateTimeBetween('-3 years', '2024-09-30'));
        $after241001 = Carbon::parse(fake()->dateTimeBetween('2024-10-01', 'now'));
        $this->generateShiptRecord($platform, [$before240930, $after241001], $price, $itemCount);
    }

    private function createOver1500Record(ShopPlatform $platform, int $price, int $itemCount) {
        $before260930 = Carbon::parse(fake()->dateTimeBetween('-3 years', '2026-09-30'));
        $after261001 = Carbon::parse(fake()->dateTimeBetween('2026-10-01', '2026-10-20'));
        $this->generateShiptRecord($platform, [$before260930, $after261001], $price, $itemCount);
    }

    private function createRegisterMailRecord(ShopPlatform $platform, int $price, int $itemCount) {
        $shiptDate = Carbon::parse(fake()->dateTimeBetween('now'));
        $this->generateShiptRecord($platform, [$shiptDate], $price, $itemCount);
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
        return ShiptLogTestHelper::generateOrderId($platform);
    }

    /**
     * 数値をランダムに分割する（整数版）
     *
     * @param int $total 分割したい合計値
     * @param int $count 分割する個数
     * @param int $minPrice 1個あたりの最低値
     * @return array
     */
    function splitNumberRandomly(int $total, int $count, int $minPrice = 100): array
    {
        return ShiptLogTestHelper::splitNumberRandomly($total, $count, $minPrice);
    }

    private function getMercariFee(Carbon $shiptDate, int $subtotal) {
        $fee = 0;
        if ($subtotal >= 300 && $subtotal < 1499) {
            $fee = $this->fetchMiniletter($shiptDate);
        } else if ($subtotal >= 1499 && $subtotal < 10000) {
            $fee = $this->fetchClickPost($shiptDate);
        } else if ($subtotal >= 10000) {
            $fee = $this->fetchRegisterMail();
        }
        return $fee;
    }

    private function getBaseFee(Carbon $shiptDate, int $subtotal) {
        $fee = 0;
        if ($subtotal >= 113 && $subtotal <= 4999) {
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
