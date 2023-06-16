<?php
namespace App\Services\Stock;

use App\Files\CsvReader;
use App\Files\Stock\StockpileCsvReader;

/**
 * 在庫管理機能のサービスクラス
 */
class StockpileService extends AbstractSmsService{

    /**
     * 出荷ログ用のCSV読み込みクラスを取得する。
     * @see CsvReader::csvReader
     * @return StockpileCsvReader
     */
    protected function csvReader() {
        return new StockpileCsvReader();
    }

    /**
     * 
     *@see AbstractSmsService::validationRules
     * @return array
     */
    protected function validationRules():array {
        return [
            'setcode' => 'required|alpha_num',
            'name' => 'required',
            'lang' => 'required|in:JP,EN,IT,CT,CS',
            'condition' => 'required|in:NM,NM-,EX+,EX,PLD',
            'quantity' => 'required|numeric',
            'isFoil' => 'nullable|in:true,false',
        ];
    }

    /**
     * 
     * @see AbstractSmsService::attributes
     * @return array
     */
    protected function attributes():array {
        return [
            'setcode' => 'セット略称',
            'name' => '商品名',
            'lang' => '言語',
            'condition' => '保存状態',
            'quantity' => '数量',
            'isFoil' => 'Foilフラグ'
        ];
    }

}