<?php
namespace App\Services\Specific;

use App\Libs\MtgJsonUtil;
use App\Services\Constant\CardConstant;
use App\Services\json\AbstractCard;
use App\Services\Specific\DefaultSpCardDetector;

class TdmSpCardDetector extends DefaultSpCardDetector {

    public function showcase(string $frame, AbstractCard $card):string {
        $isFullart = MtgJsonUtil::isTrue('isFullArt', $card->getJson());
        $subtype = current($card->subtypes());
        $name = $card->getJson()[CardConstant::NAME];
        if ($isFullart) {
            if ($this->isSaga($subtype) || $this->isSiege($name)) {
                return 'borderless';
            }
            return 'clan';
        }
        // 「ドラコニック」ショーケース
        $dragon = 'Dragon';
        if (strpos($name, $dragon) || $subtype == $dragon) {
            return 'dragonic';
        }
        return $frame;
    }

    /**
     * カードが英雄譚か判別する。
     */
    private function isSaga(string $subtype):bool {
        return $subtype == 'Saga';
    }

    private function isSiege(string $name):bool {
        return str_ends_with($name, 'Siege');
    }


    /**
     * TDM用ボーダーレスを判別する。
     *
     * @param AbstractCard $card
     * @return string
     */
    public function borderless(AbstractCard $card):string {
        $layout = $card->getJson()['layout'];
        if ($layout == 'reversible_card') {
            return $layout;
        }
        return "borderless";
    }

    public function isExclude(array $json):bool {
        // Foilカードのみ
        $hasFoil = boolval($json['hasFoil']);
        $hasNonFoil = boolval($json['hasNonFoil']);
        if ($hasFoil && !$hasNonFoil) {
            return true;
        }

        // 「ドラゴンの眼」フルアート
        $number = intval($json[CardConstant::NUMBER]);
        if ($number >= 287 & $number <= 291) {
            return true;
        }

        // ハロー・Foil
        $promotypes = $json[CardConstant::PROMOTYPES];
        return in_array('halofoil', $promotypes);
    }
}