<?php
namespace App\Http\Validator;
use App\Http\Validator\AbstractCsvValidator;
use App\Services\Constant\ShiptConstant as ShiptCon;
use App\Services\Constant\StockpileHeader as Header;

class ShippingValidator extends AbstractCsvValidator {
    /**
     * @see AbstractCsvValidator::validationRules
     */
   protected function validationRules():array {
    return [
        ShiptCon::BUYER => 'required',
        Header::CONDITION => 'nullable|in:NM,NM-,EX+,EX,PLD',
        Header::QUANTITY => 'required|numeric',
        ShiptCon::SHIPPING_DATE => 'required|date_format:YYYY/m/d'
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