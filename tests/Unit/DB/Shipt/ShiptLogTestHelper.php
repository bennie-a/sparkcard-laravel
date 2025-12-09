<?php
namespace Tests\Unit\DB\Shipt;

use App\Exceptions\api\NotFoundException;
use App\Models\Stockpile;
use App\Services\Constant\ShiptConstant as SC;
use App\Services\Constant\CardConstant as CC;
use App\Services\Constant\GlobalConstant as GC;

use App\Services\Constant\StockpileHeader;

class ShiptLogTestHelper
{
    /**
     * 購入者情報をランダムで作成する。
     *
     * @return array
     */
    public static  function createBuyerInfo(int $itemCount, string $shiptDate,
                                                            bool $isFoil = false, bool $isPromo = false, int $quantity = 1):array {
        $items = [];
        for ($i=0; $i < $itemCount; $i++) {
            $items[] = self::createItemInfo($isFoil, $isPromo, $quantity);
        }
        return [
            SC::ORDER_ID => self::createOrderId(),
            SC::BUYER => fake()->name(),
            SC::POSTAL_CODE => fake()->postcode1()."-".fake()->postcode2(),
            SC::STATE => fake()->prefecture(),
            SC::CITY => fake()->city(),
            SC::ADDRESS_1 => fake()->streetAddress(),
            SC::ADDRESS_2 => fake()->secondaryAddress(),
            SC::SHIPPING_DATE => $shiptDate,
            SC::ITEMS => $items
        ];
    }

    /**
     * 商品情報を1件作成する。
     *
     * @param integer $foiltypeId
     * @param integer $promotypeId
     * @return array
     */
    public static  function createItemInfo(bool $isFoil, bool $isPromo, int $quantity = 1):array {
        $stock = self::getRandomStock($isFoil, $isPromo, $quantity);
        return [GC::ID => $stock->id, SC::PRODUCT_NAME => self::product_name($stock),
                    StockpileHeader::QUANTITY => fake()->numberBetween(1, $stock->quantity),
                    SC::PRODUCT_PRICE => fake()->numberBetween(300, 10000), SC::DISCOUNT_AMOUNT => 0];
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
    public static function product_name(Stockpile $stock, bool $isSet = false): string {
        $card = $stock->cardinfo;
        $exp = $stock->cardinfo->expansion;
        $foil = $stock->cardinfo->foiltype;
        $promo = $stock->cardinfo->promotype;
        return "【{$exp->attr}】".
                ($foil->id != 1 ? "【{$foil->name}】" : "").
                "{$card->name}".
                ($promo->id != 1 ? "≪{$promo->name}≫" : "").
                ($isSet && $stock->quantity > 1 ? "{$stock->quantity}枚セット" : "").
                "[$stock->language]"."[{$card->color_id}]";
    }

    /**
     * 注文番号をランダムで作成する。
     *
     * @return string
     */
    private static function createOrderId(): string {
        return uniqid('order_');
    }


    /**
     * 注文CSV用ヘッダー行を取得する。
     *
     * @return array
     */
    public static  function getHeader() {
        $header = [
                                SC::ORDER_ID, SC::BUYER, SC::POSTAL_CODE, SC::STATE,
                                SC::CITY, SC::ADDRESS_1, SC::ADDRESS_2, SC::SHIPPING_DATE,
                                SC::PRODUCT_ID, SC::PRODUCT_NAME, StockpileHeader::QUANTITY,
                                SC::PRODUCT_PRICE, SC::DISCOUNT_AMOUNT
                            ];
        return self::arrayToCsvString($header);
    }

    /**
     * CSV1行分の文字列を作成する。
     *
     * @param array $buyers
     * @return string
     */
    public static  function createCsvLine(array $buyers):string {
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
     * @param [type] $array
     * @return string
     */
    public static function arrayToCsvString($array):string
    {
        return implode(',', $array);
    }

}
