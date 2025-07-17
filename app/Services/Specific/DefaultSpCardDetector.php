<?php

namespace App\Services\Specific;

use App\Libs\MtgJsonUtil;
use App\Services\interfaces\SpCardDetectorInterface;
use App\Services\json\AbstractCard;
use App\Services\Constant\CardConstant as Con;
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
        // 拡張アート
        if (MtgJsonUtil::hasKey(Con::FRAME_EFFECT, $json)) {
            return in_array(Con::EXTENDED_ART, $json[Con::FRAME_EFFECT]);
        }
        return false;
    }
}