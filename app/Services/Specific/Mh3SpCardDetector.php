<?php
namespace App\Services\Specific;

use App\Services\json\AbstractCard;
/**
 * 『モダンホライゾン3』の特別版判別クラス
 */
class Mh3SpCardDetector extends DefaultSpCardDetector {

    /**
     * 旧枠対応
     *
     * @param AbstractCard $card
     * @return string
     */
    public function othercase(AbstractCard $card):string {
        $version = $card->frameVersion();
        if ($version === '1997') {
            return 'oldframe';
        }
        return '';
    }
}