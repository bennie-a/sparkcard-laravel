<?php
namespace App\Services\Specific;

use App\Services\json\AbstractCard;
/**
 * 『エルドレインの森』の特別版判別クラス
 */
class WoeSpCardDetector extends DefaultSpCardDetector {

    /**
     * 出来事ソーサリーカードを除外する。
     *
     * @param array $json
     * @return boolean
     */
    public function isExclude(array $json):bool {
        return strcmp($json["type"], "Sorcery — Adventure") == 0;
    }
}