<?php
namespace App\Services\Stock;

use App\Files\CsvReader;
use App\Files\Stock\StockpileCsvReader;
use App\Models\CardInfo;
use App\Models\Expansion;
use App\Models\Stockpile;

/**
 * 在庫管理機能のサービスクラス
 */
class StockpileService extends AbstractSmsService{

    const SETCODE = 'setcode';
    const NAME = 'name';
    const LANG = 'lang';
    const CONDITION = 'condition';
    const QUANTITY = 'quantity';
    const IS_FOIL = 'isFoil';

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
            self::SETCODE => 'required|alpha_num',
            self::NAME => 'required',
            self::LANG => 'required|in:JP,EN,IT,CT,CS',
            self::CONDITION => 'required|in:NM,NM-,EX+,EX,PLD',
            self::QUANTITY => 'required|numeric',
            self::IS_FOIL => 'nullable|in:true,false',
        ];
    }

    /**
     * 
     * @see AbstractSmsService::attributes
     * @return array
     */
    protected function attributes():array {
        return [
            self::SETCODE => 'セット略称',
            self::NAME => '商品名',
            self::LANG => '言語',
            self::CONDITION => '保存状態',
            self::QUANTITY => '数量',
            self::IS_FOIL => 'Foilフラグ'
        ];
    }

    protected function store(int $key, array $row) {
            $number = $key + 2;
            $isFoil = empty($row[self::IS_FOIL]) ? filter_var($row[self::IS_FOIL], FILTER_VALIDATE_BOOLEAN) : false;
            // カード情報を取得する
            $info = CardInfo::findSingleCard($row[self::SETCODE], $row[self::NAME], $isFoil);
            if (empty($info)) {
                parent::addError([$number => 'カードマスタ情報なし']);
                return;
            }
            $lang = $row[self::LANG];
            $condition = $row[self::CONDITION];
            // 在庫情報の重複チェック
            $isExists = Stockpile::isExists($info->id, $lang, $condition);
            if ($isExists == true) {
                parent::addIgnore([$number => '在庫情報が登録済み']);
                return;
            }
            Stockpile::create(['card_id' => $info->id, 'language' => $lang,
                     self::CONDITION => $condition, self::QUANTITY => $row[self::QUANTITY]]);
            parent::addSuccess($number);
    }
}