<?php
namespace App\Http\Validator;
use App\Http\Validator\AbstractCsvValidator;
use App\Rules\Halfsize;
use App\Services\Constant\StockpileHeader;

class StockpileValidator extends AbstractCsvValidator {
    /**
     * @see AbstractCsvValidator::validationRules
     */
   protected function validationRules():array {
    return [
        StockpileHeader::SETCODE => 'nullable|alpha_num',
        StockpileHeader::NAME => 'required',
        StockpileHeader::LANG => 'nullable|in:JP,EN,IT,CT,CS',
        StockpileHeader::CONDITION => 'nullable|in:NM,NM-,EX+,EX,PLD',
        StockpileHeader::QUANTITY => 'required|numeric',
        StockpileHeader::IS_FOIL => 'nullable|in:true,false',
        StockpileHeader::EN_NAME => ['nullable', new Halfsize()]
    ];
   }

    /**
     * 
     * @see AbstractCsvValidator::attributes
     * @return array
     */
   protected function attributes():array {
    return [
        StockpileHeader::SETCODE => 'セット略称',
        StockpileHeader::NAME => '商品名',
        StockpileHeader::LANG => '言語',
        StockpileHeader::CONDITION => '保存状態',
        StockpileHeader::QUANTITY => '数量',
        StockpileHeader::IS_FOIL => 'Foilフラグ',
        StockpileHeader::EN_NAME => '英語カード名'
    ];
   }
}