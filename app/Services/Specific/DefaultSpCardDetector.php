<?php

namespace App\Services\Specific;

use App\Services\interfaces\SpCardDetectorInterface;
use App\Services\json\AbstractCard;
/**
 *デフォルトの特別版判別クラス
 */
class DefaultSpCardDetector implements SpCardDetectorInterface {

    public function showcase(string $frame, AbstractCard $card):string {
        return $frame;
    }

    public function borderless(AbstractCard $card):string {
        return "borderless";
    }

    public function othercase(AbstractCard $card):string {
        return '';
    }

    public function isExclude(array $json):bool {
        return false;
    }
}