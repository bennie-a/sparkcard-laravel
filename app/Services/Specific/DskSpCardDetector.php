<?php
namespace App\Services\Specific;

use App\Enum\CardColor;
use App\Services\interfaces\SpCardDetectorInterface;
use App\Services\json\AbstractCard;
use App\Services\Constant\CardConstant as Con;
/**
 * 『ダスクモーン：戦慄の館』の特別版判別クラス
 */
class DskSpCardDetector implements SpCardDetectorInterface {

    public function showcase(string $promotype, AbstractCard $card):string {
        return 'paranormal';
    }
    
    public function borderless(AbstractCard $card):string {
        $isLand = $this->isLand($card->color());
        // 「部屋」カードかどうか判別する。
        $isRoom = $this->isRoom($card->subtypes());
        // プレインズウォーカーかどうか判別する。
        $isPlainswalker = $this->isPlainsWalker($card->types());
        if ($isLand || $isRoom || $isPlainswalker) {
            return Con::BORDERLESS;
        }
        return 'mirror';
    }

    /**
     * 部屋カードかどうか判別する。
     *
     * @param array $subtypes
     * @return boolean
     */
    private function isRoom(array $subtypes) {
        return in_array('Room', $subtypes);
    }

    /**
     * 土地カードかどうか判別する。
     *
     * @param string $color
     * @return boolean
     */
    private function isLand(string $color) {
        return $color === CardColor::LAND->value;
    }

    /**
     * カードタイプがプレインズウォーカーかどうか判別する。
     *
     * @param array $types
     * @return boolean
     */
    private function isPlainsWalker(array $types) {
        return in_array('Planeswalker', $types);
    }
}