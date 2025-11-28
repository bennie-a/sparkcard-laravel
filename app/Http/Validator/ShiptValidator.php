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
        ShiptCon::BUYER => 'required',
        Header::CONDITION => 'nullable|in:NM,NM-,EX+,EX,PLD',
        Header::QUANTITY => 'required|numeric',
        ShiptCon::SHIPPING_DATE => 'date_format:Y/m/d'
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
}