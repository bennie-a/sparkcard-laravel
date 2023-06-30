<?php
namespace App\Services\Stock;

use App\Exceptions\NotFoundException;
use App\Facades\ScryfallServ;

use App\Facades\CardInfoServ;
use App\Facades\ExService;
use App\Facades\MtgDev;
use App\Files\CsvReader;
use App\Files\Stock\StockpileCsvReader;
use App\Models\CardInfo;
use App\Models\Expansion;
use App\Models\Stockpile;
use App\Rules\Halfsize;
use App\Services\Constant\StockpileHeader;
use App\Http\Validator\StockpileValidator;

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

    // /**
    //  * 
    //  *@see AbstractSmsService::validationRules
    //  * @return array
    //  */
    // protected function validationRules():array {
    //     return [
    //         StockpileHeader::SETCODE => 'nullable|alpha_num',
    //         StockpileHeader::NAME => 'required',
    //         StockpileHeader::LANG => 'nullable|in:JP,EN,IT,CT,CS',
    //         StockpileHeader::CONDITION => 'nullable|in:NM,NM-,EX+,EX,PLD',
    //         StockpileHeader::QUANTITY => 'required|numeric',
    //         StockpileHeader::IS_FOIL => 'nullable|in:true,false',
    //         StockpileHeader::EN_NAME => ['nullable', new Halfsize()]
    //     ];
    // }

    // /**
    //  * 
    //  * @see AbstractSmsService::attributes
    //  * @return array
    //  */
    // protected function attributes():array {
    //     return [
    //         StockpileHeader::SETCODE => 'セット略称',
    //         StockpileHeader::NAME => '商品名',
    //         StockpileHeader::LANG => '言語',
    //         StockpileHeader::CONDITION => '保存状態',
    //         StockpileHeader::QUANTITY => '数量',
    //         StockpileHeader::IS_FOIL => 'Foilフラグ',
    //         StockpileHeader::EN_NAME => '英語カード名'
    //     ];
    // }

    protected function store(int $key, array $row) {
        try {
            $number = $key + 2;
            logger()->info('Start Import', ['number' => $number]);
            // APIで検索
            // 件数が0件、もしくは2件以上ならエラー
            // エキスパンションをDBで存在チェック
            $setcode = $row[self::SETCODE];
            if(\ExService::isExistByAttr($setcode) == false) {
                \ExService::storeByScryfall($setcode, 'レガシー');
            }

            $isFoil = !empty($row[self::IS_FOIL]) ? filter_var($row[self::IS_FOIL], FILTER_VALIDATE_BOOLEAN) : false;
            // カード情報を取得する
            $cardname = $row[self::NAME];
            $info = CardInfo::findSingleCard($setcode, $cardname, $isFoil);

            // カード情報なし⇒新たに登録する。
            if (empty($info)) {
                if (preg_match('/.*≪(.+?)≫$/', $cardname) == 1) {
                    $this->addError($number, '特別版はマスタ登録できません');
                    return;
                }
                $info = \CardInfoServ::postByScryfall($setcode, $row, $isFoil);
            }
            $lang = !empty($row[self::LANG]) ? $row[self::LANG] : 'JP';
            $condition = !empty($row[self::CONDITION]) ? $row[self::CONDITION]  : 'EX+';
            // 在庫情報の重複チェック
            $isExists = Stockpile::isExists($info->id, $lang, $condition);
            if ($isExists == true) {
                parent::addSkip($number, '在庫情報が登録済み');
                return;
            }
            Stockpile::create(['card_id' => $info->id, 'language' => $lang,
                     self::CONDITION => $condition, self::QUANTITY => $row[self::QUANTITY]]);
            parent::addSuccess($number);
        } catch (NotFoundException $e) {
            $this->addError($number, $e->getMessage());
        }
    }

    protected function getValidator() {
        return new StockpileValidator();
    }


}