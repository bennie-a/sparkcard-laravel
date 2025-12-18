<?php
namespace App\Http\Validator;
use App\Http\Validator\AbstractCsvValidator;
use App\Services\Constant\ShiptConstant as ShiptCon;
use App\Services\Constant\StockpileHeader as Header;

/**
 * 注文CSV1行分のバリデーションクラス
 */
class ShiptValidator extends AbstractCsvValidator {
    /**
     * @see AbstractCsvValidator::validationRules
     */
   protected function validationRules():array {
    return [
        ShiptCon::ORDER_ID => 'required',
        ShiptCon::BUYER => 'required',
        ShiptCon::SHIPPING_DATE => 'date_format:Y/m/d',
        ShiptCon::POSTAL_CODE => 'required|regex:/^\d{3}-\d{4}$/',
        ShiptCon::STATE => 'required|in:北海道,青森県,岩手県,宮城県,秋田県,山形県,福島県,茨城県,栃木県,群馬県,埼玉県,千葉県,東京都,神奈川県,新潟県,富山県,石川県,福井県,山梨県,長野県,岐阜県,静岡県,愛知県,三重県,滋賀県,京都府,大阪府
                                                            ,兵庫県,奈良県,和歌山県,鳥取県,島根県,岡山県,広島県,山口県,徳島県,香川県,愛媛県,高知県,福岡県,佐賀県,長崎県,熊本県,大分県,宮崎県,鹿児島県,沖縄県',
        ShiptCon::CITY => 'required|string|regex:/[市区町村郡]/u',
        ShiptCon::ADDRESS_1 => 'required|string',
        ShiptCon::PRODUCT_ID => 'required|numeric|min:1',
        ShiptCon::PRODUCT_NAME => 'required',
        Header::QUANTITY => 'required|numeric|min:1',
        ShiptCon::PRODUCT_PRICE => 'required|numeric|min:50',
        ShiptCon::DISCOUNT_AMOUNT => 'required|numeric|min:0'
    ];
   }

 /**
     *
     * @see AbstractCsvValidator::attributes
     * @return array
     */
   protected function attributes():array {
        return [
            ShiptCon::SHIPPING_DATE => '発送日',
            ShiptCon::PRODUCT_PRICE => '商品価格',
            ShiptCon::DISCOUNT_AMOUNT => '割引額',
            ShiptCon::SINGLE_PRICE => '単価',
            ShiptCon::TOTAL_PRICE => '支払い価格',
            ShiptCon::PRODUCT_ID => '商品コード',
            ShiptCon::PRODUCT_NAME =>'商品名',
            Header::QUANTITY => '出荷枚数',
            ShiptCon::DISCOUNT_AMOUNT => 'クーポン割引額'
        ];
    }

    /**
     * 各機能固有のメッセージを取得する。
     *
     * @return array
     */
    public function messages():array {
        return [
            'shipping_postal_code.regex' => '郵便番号は「123-4567」の形式で入力してください。',
            'shipping_city.regex' => '市区町村名は「市」「区」「町」「村」「郡」を最後につけてください。',
        ];
    }

}
