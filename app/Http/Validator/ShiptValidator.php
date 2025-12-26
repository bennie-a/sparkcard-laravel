<?php
namespace App\Http\Validator;
use App\Http\Validator\AbstractCsvValidator;
use App\Rules\DateFormatRule;
use App\Rules\Halfsize;
use App\Rules\PostalCodeRule;
use App\Rules\StateRule;
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
        ShiptCon::ORDER_ID => ['required', new Halfsize()],
        ShiptCon::BUYER => 'required',
        ShiptCon::SHIPPING_DATE => DateFormatRule::slashRules(),
        ShiptCon::POSTAL_CODE => ['required', PostalCodeRule::rules()],
        ShiptCon::STATE => ['required', StateRule::rules()],
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
