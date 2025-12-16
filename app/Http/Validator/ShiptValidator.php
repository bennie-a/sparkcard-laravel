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
        ShiptCon::PRODUCT_ID => 'required|numeric',
        ShiptCon::PRODUCT_NAME => 'required',
        Header::QUANTITY => 'required|numeric',
        ShiptCon::PRODUCT_PRICE => 'required|numeric',
        ShiptCon::POSTAL_CODE => 'required|regex:/^\d{3}-\d{4}$/'

    ];
   }

 /**
     *
     * @see AbstractCsvValidator::attributes
     * @return array
     */
   protected function attributes():array {
        return [
        ];
    }

    public function messages()
    {
        return [
            ShiptCon::POSTAL_CODE.'.regex' => __('validation.custom.shipping_postal_code.regex')
        ];
    }

}
