<?php
namespace App\Http\Validator;
use App\Http\Validator\AbstractCsvValidator;
use App\Services\Constant\StockpileHeader as Header;

class ShippingValidator extends AbstractCsvValidator {
    /**
     * @see AbstractCsvValidator::validationRules
     */
   protected function validationRules():array {
    return [
        Header::BUYER => 'required',
        Header::CONDITION => 'nullable|in:NM,NM-,EX+,EX,PLD',
        Header::QUANTITY => 'required|numeric'
    ];
   }

    /**
     * 
     * @see AbstractCsvValidator::attributes
     * @return array
     */
   protected function attributes():array {
    return [
        Header::BUYER => '購入者名',
        Header::CONDITION => '保存状態',
        Header::QUANTITY => '数量',
    ];
   }
}