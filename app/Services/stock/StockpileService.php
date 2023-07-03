<?php
namespace App\Services\Stock;

use App\Exceptions\ConflictException;
use App\Exceptions\NotFoundException;
use App\Files\Stock\StockpileCsvReader;
use App\Models\CardInfo;
use App\Models\Stockpile;
use App\Services\Constant\StockpileHeader as Header;
use App\Services\Stock\StockpileRow;

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
     * @see AbstractSmsService::store
     * @param StockpileRow $row
     * @return void
     */
    protected function store(StockpileRow $row) {
        try {
            $number = $row->number();
            $strategy = $row->strategy();
            $setcode = $strategy->getSetCode($row);
            if(\ExService::isExistByAttr($setcode) == false) {
                \ExService::storeByScryfall($setcode, 'レガシー');
            }

            $isFoil = $row->isFoil();
            $cardname = $row->name();

            // カード情報を取得する
            $info = CardInfo::findSingleCard($setcode, $cardname, $isFoil);
            // カード情報なし⇒新たに登録する。
            if (empty($info)) {
                if (preg_match('/.*≪(.+?)≫$/', $cardname) == 1) {
                    $this->addError($number, '特別版はマスタ登録できません');
                    return;
                }
                $info = \CardInfoServ::postByScryfall($setcode, $cardname, $row->en_name(),$isFoil);
            }
            $lang = $row->language();
            $condition = $row->condition();
            // 在庫情報の重複チェック
            $isExists = Stockpile::isExists($info->id, $lang, $condition);
            if ($isExists == true) {
                parent::addSkip($number, '在庫情報が登録済み');
                return;
            }
            Stockpile::create(['card_id' => $info->id, 'language' => $lang,
                     Header::CONDITION => $condition, Header::QUANTITY => $row->quantity()]);
            parent::addSuccess($number);

        } catch (NotFoundException | ConflictException $e) {
            $this->addError($number, $e->getMessage());
        }
    }

    /**
     * 
     * @see AbstractSmsService::createRow
     */
    protected function createRow(int $index, array $row) {
        return new StockpileRow($index, $row);
    }
}