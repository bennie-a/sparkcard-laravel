<?php
namespace App\Services\Specific;

use App\Libs\MtgJsonUtil;
use App\Services\json\AbstractCard;
use App\Services\Constant\CardConstant as Con;

/**
 * 『霊気走破』の特別版判別クラス
 */
class DftCardDetector extends DefaultSpCardDetector {

    public function borderless(AbstractCard $card):string {
        $subtypes = $card->subtypes();
        // 「グラフィティ・ジャイアント」ボーダーレス
        if (in_array("God", $subtypes) || in_array("Construct", $subtypes)) {
            return "graffgiant";
        }
        // 「最大出力」ボーダーレス
        if (in_array("Vehicle", $subtypes)) {
            return "maxspeed";
        }

        $frames = MtgJsonUtil::getIfExists(Con::FRAME_EFFECT, $card->getJson());
        if (in_array("legendary", $frames)) {
            return "borderless";
        }

        // 「ワルなライダー」ボーダーレス
        return "badassrider";
    }
}