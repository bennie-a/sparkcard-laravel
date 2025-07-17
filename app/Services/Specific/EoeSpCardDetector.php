<?php
namespace App\Services\Specific;

use App\Enum\CardColor;
use App\Models\MainColor;
use App\Services\Constant\CardConstant;
use App\Services\json\AbstractCard;
use App\Services\Specific\DefaultSpCardDetector;

class EoeSpCardDetector extends DefaultSpCardDetector {

    public function showcase(string $frame, AbstractCard $card):string {
        if ($frame == "enchantment") {
            return 'surreal_space';       
        }
        $color = $card->color();
        if ($frame == CardConstant::EXTENDED_ART && $color == CardColor::LAND->value) {
            return 'observation';
        }
        return CardConstant::BORDERLESS;
    }

    public function isExclude(array $json):bool {
        $number = intval($json[CardConstant::NUMBER]);
        if ($number >= 277 && $number <= 286) {
            return false;
        }
        return parent::isExclude($json);
    }
}