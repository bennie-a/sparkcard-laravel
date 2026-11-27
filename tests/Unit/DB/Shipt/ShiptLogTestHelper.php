<?php
namespace Tests\Unit\DB\Shipt;

use App\Enum\Shipt\MercariCsvType;
use App\Enum\ShiptMethod;
use App\Enum\ShopPlatform;
use App\Exceptions\api\NotFoundException;
use App\Models\Stockpile;
use App\Services\Constant\ShiptConstant as SC;
use App\Services\Constant\CardConstant as CC;
use App\Services\Constant\GlobalConstant as GC;
use App\Services\Constant\GlobalConstant;
use App\Services\Constant\StockpileHeader;
use Tests\Util\TestDateUtil;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * 注文CSV用テストデータを作成するクラス
 */
class ShiptLogTestHelper
{
    /**
     *商品情報つき 購入者情報をランダムで作成する。
     *
     * @return array
     */
    public static function createBuyerInfo(int $itemCount,
                                                            bool $isFoil = false, bool $isPromo = false,
                                                             int $quantity = 1, ShopPlatform $platform = ShopPlatform::MERCARI):array {
        $items = [];
        for ($i=0; $i < $itemCount; $i++) {
            $items[] = self::createItemInfo($isFoil, $isPromo, $quantity);
        }

        $orderPrice = collect($items)->sum(SC::SUBTOTAL);
        $buyerInfo = self::createBuyerInfoOnly();
        $buyerInfo[SC::ORDER_PRICE] = $orderPrice;
        $buyerInfo[SC::ITEMS] = $items;
        $buyerInfo[SC::ORDER_ID] = self::generateOrderId($platform);
        return $buyerInfo;
    }

    /**
     * 出荷情報登録APIのリクエストJSONを
     * ランダムで作成する。
     *
     * @param integer $itemCount
     * @param ShopPlatform $platform
     * @return array
     */
    public static function createStoreRequest($itemCount = 1, ShopPlatform $platform = ShopPlatform::MERCARI):array {
        $buyerInfo = self::createBuyerInfo($itemCount, false, false, 1, $platform);
        $buyerInfo[SC::ADDRESS] = $buyerInfo[SC::STATE].$buyerInfo[SC::CITY].
                                                                $buyerInfo[SC::ADDRESS_1].' '.$buyerInfo[SC::ADDRESS_2];
        $buyerInfo[SC::ZIPCODE] = $buyerInfo[SC::POSTAL_CODE];
        $buyerInfo[SC::SHIPT_DATE] = TestDateUtil::formatToday();
        $buyerInfo[SC::PLATFORM] = $platform->value;
        $buyerInfo[SC::ITEM_SUBTOTAL] = rand(1, 20000);
        $buyerInfo[SC::DISCOUNT_AMOUNT] = rand(0, 400);
        $buyerInfo[SC::GRAND_TOTAL] = $buyerInfo[SC::ITEM_SUBTOTAL] - $buyerInfo[SC::DISCOUNT_AMOUNT];

        $fee = ShiptMethod::findByPrice($buyerInfo[SC::ITEM_SUBTOTAL]);
        $buyerInfo[SC::FEE] = [GC::ID => $fee->id];


        unset($buyerInfo[SC::STATE]);
        unset($buyerInfo[SC::CITY]);
        unset($buyerInfo[SC::ADDRESS_1]);
        unset($buyerInfo[SC::ADDRESS_2]);
        unset($buyerInfo[SC::POSTAL_CODE]);

        $items = array_map(function($item) {
            return [
                GC::ID => $item[GC::ID],
                SC::SHIPMENT => $item[StockpileHeader::QUANTITY],
                SC::UNIT_PRICE => $item[SC::PRODUCT_PRICE],
                SC::SUBTOTAL => fake()->numberBetween(50, 200),
                SC::IS_REGISTERED => false,
            ];
        }, $buyerInfo[SC::ITEMS]);

        $buyerInfo[SC::ITEMS] = $items;
        $buyerInfo[SC::ITEM_COUNT] = count($items);
        return $buyerInfo;
    }

    /**
     * 購入者情報をランダムで作成する。
     *
     * @return array
     */
    public static function createBuyerInfoOnly():array {
        return [
            SC::TYPE => MercariCsvType::ORDER->value,
            SC::ORDER_ID => self::generateOrderId(ShopPlatform::MERCARI),
            SC::BUYER => fake()->name(),
            SC::SHIPPING_PRICE => 0,
            SC::POSTAL_CODE => fake()->postcode1()."-".fake()->postcode2(),
            SC::STATE => fake()->prefecture(),
            SC::CITY => fake()->city(),
            SC::ADDRESS_1 => fake()->streetAddress(),
            SC::ADDRESS_2 => fake()->secondaryAddress(),
            SC::DISCOUNT_AMOUNT => 0
        ];
    }

    /**
     * 発送日が今日の注文情報を取得する。
     *
     * @return array
     */
    public static  function createTodayOrderInfos(ShopPlatform $platform = ShopPlatform::MERCARI): array {
        return ShiptLogTestHelper::createBuyerInfo(1, false, false, 1, $platform);
    }


    /**
     * 商品情報を1件作成する。
     *
     * @param bool $isFoil
     * @param bool $isPromo
     * @return array
     */
    public static  function createItemInfo(bool $isFoil, bool $isPromo, int $quantity = 1):array {
        $stock = self::getRandomStock($isFoil, $isPromo, $quantity);
        $quantity = fake()->numberBetween(1, $stock->quantity);
        $price = fake()->numberBetween(300, 10000);
        return [SC::TYPE => MercariCsvType::PRODUCT->value, GC::ID => $stock->id,
                    SC::PRODUCT_NAME => self::product_name($stock),
                    StockpileHeader::QUANTITY => $quantity,
                    SC::PRODUCT_PRICE => $price,
                    SC::SUBTOTAL => $quantity * $price, SC::SHIPPING_METHOD => ShiptMethod::TBD->value];
    }

    /**
     * 条件に合った在庫情報を取得する。
     *
     * @param boolean $isFoil true:Foil、false:Non-Foil
     * @param boolean $isPromo true:特別版、false:通常版
     * @param integer $quantity 枚数
     * @return Stockpile
     */
    private static function getRandomStock(bool $isFoil, bool $isPromo, int $quantity = 1): Stockpile {
        $isFoilOpe = !$isFoil ? '=' : '<>';
        $isPromoOpe = !$isPromo ? '=' : '<>';
        $stock = Stockpile::inRandomOrder()->
                            where(StockpileHeader::QUANTITY, '>=', $quantity)
                            ->whereHas('cardinfo', function($query) use($isFoilOpe, $isPromoOpe){
                                $query->where(CC::FOIL_ID, $isFoilOpe, 1)
                                ->where(CC::PROMO_ID, $isPromoOpe, 1);
                            })->first();
        if(!$stock) {
            throw new NotFoundException('在庫情報がありません。Foil:'.($isFoil ? 'あり' : 'なし').' Promo:'.($isPromo ? 'あり' : 'なし'));
        }
        return $stock;
    }

    /**
     * 在庫情報から商品名を作成する。
     *
     * @param Stockpile $stock
     * @param $isSet true:セット販売、false:単品
     * @return string
     */
    public static function product_name(Stockpile $stock, bool $isSet = false, int $setSheet = 1): string {
        $card = $stock->cardinfo;
        $exp = $stock->cardinfo->expansion;
        $foil = $stock->cardinfo->foiltype;
        $promo = $stock->cardinfo->promotype;
        return "【{$exp->attr}】".
                ($foil->id != 1 ? "【{$foil->name}】" : "").
                "{$card->name}".
                ($promo->id != 1 ? "≪{$promo->name}≫" : "").
                ($isSet && $stock->quantity > 1 ? "{$setSheet}枚セット" : "").
                "[$stock->language]"."[{$card->color_id}]";
    }

    /**
     * 注文CSV用ヘッダー行を取得する。
     *
     * @return array
     */
    public static  function getHeader() {
        $header = [
                                SC::TYPE,
                                 SC::ORDER_ID,
                                 SC::BUYER,
                                 SC::PRODUCT_ID,
                                 SC::PRODUCT_NAME,
                                 StockpileHeader::QUANTITY,
                                 SC::ORDER_PRICE,
                                 SC::PRODUCT_PRICE,
                                SC::SHIPPING_PRICE,
                                 SC::DISCOUNT_AMOUNT,
                                 SC::SHIPPING_METHOD,
                                SC::POSTAL_CODE,
                                SC::STATE,
                                SC::CITY,
                                SC::ADDRESS_1,
                                SC::ADDRESS_2,
                            ];
        return self::arrayToCsvString($header);
    }

    /**
     * CSV1行分の文字列を作成する。
     *
     * @param array $buyers
     * @return string
     */
    public static function createCsvLine(array $buyers):string {
        $implode = '';
        foreach($buyers as $buyer) {
            $items = $buyer[SC::ITEMS];
            unset($buyer[SC::ITEMS]);
            $buyerLine = array_values($buyer);
            foreach($items as $item) {
                $oneline = $buyerLine;
                $oneline = array_merge($buyerLine, array_values($item));
                $implode .= ShiptLogTestHelper::arrayToCsvString($oneline)."\n";
                $oneline = [];
            }
        }
        return $implode;
    }

    /**
     * 配列を','区切りの文字列に変換する。
     *
     * @param array $array
     * @return string
     */
    public static function arrayToCsvString(array $array):string
    {
        return implode(',', $array);
    }

    /**
     * 指定した文字列から日付を取得する。
     * td:今日、tmr:明日、yd:昨日、'':空文字
     * @param string $date
     * @return string
     */
    public static function getShiptDate(string $date): string {
        $date = match($date) {
            'td' => TestDateUtil::formatToday(),
            'tmr' => TestDateUtil::formatTomorrow(),
            'yd' => TestDateUtil::formatYesterday(),
            '' => ''
        };
        logger()->info("Testing shipping date: {$date}");
        return $date;
    }

    public static function padBlankToArray(array $array, int $length): array {
        return array_pad($array, count($array) + $length, "");
    }

    /**
     * キーから属性名を取得する。
     *
     * @param string $key
     * @return string
     */
    public static function attribute(string $key):string {
        return match($key) {
            SC::PLATFORM => 'プラットフォーム',
            SC::ORDER_ID => '注文番号',
            SC::BUYER => '購入者名',
            SC::ZIPCODE => '郵便番号',
            SC::ADDRESS => '住所',
            SC::ITEM_SUBTOTAL => '合計金額',
            SC::DISCOUNT_AMOUNT => 'クーポン割引額',
            SC::GRAND_TOTAL => '最終請求金額',
            SC::ITEMS => '商品情報',
            GlobalConstant::ID => '在庫ID',
            SC::SHIPMENT => '出荷枚数',
            SC::SUBTOTAL => '小計',
            SC::UNIT_PRICE => '1枚あたりの単価',
            SC::IS_REGISTERED => '登録済みフラグ',
            SC::SHIPT_DATE => '発送日',
            SC::FEE => '送料',
            SC::FEE.'.'.GlobalConstant::ID => '送料ID',
            default => $key,
        };
    }

    /**
     * プラットフォームに応じて注文番号をランダムに生成する。
     *
     * @return string
     */
    public static function generateOrderId(ShopPlatform $platform): string
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
     * @param int $minPrice 1個あたりの最低値
     * @return array 分割した数値
     */
    public static function splitNumberRandomly(int $total, int $count, int $minPrice = 100): array
    {
        // 安全チェック：全商品に最低金額を配っても合計金額を超えないか確認
        if ($total < ($count * $minPrice)) {
            throw new InvalidArgumentException(
                "金額が不足しています。商品数 {$count} 個で最低金額 {$minPrice} 円の場合、
                                    合計は最低でも " . ($count * $minPrice) . " 円以上にする必要があります。"
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
