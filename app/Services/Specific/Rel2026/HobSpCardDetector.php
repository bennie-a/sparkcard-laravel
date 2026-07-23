<?php
namespace App\Services\Specific\Rel2026;

use App\Services\json\AbstractCard;
use App\Services\Specific\DefaultSpCardDetector;
use Override;

/**
 * 『ホビット[HOB]』の特別版を判別するクラス。
 * ※このセットにはショーケース版が存在しない。
 */
class HobSpCardDetector extends DefaultSpCardDetector {

    public function borderless(AbstractCard $card):string {
        $number = $card->number();
        // 計略カード
        if ($number >= 201 && $number <= 208) {
            return 'hob_strategy';
        }

        // 「竜の財宝」フレーム
        if ($number >= 216 && $number <= 237) {
            return 'dragons_hoard';
        }

        // ブックカバー・カード
        if ($number >= 240 && $number <= 248) {
            return 'book_cover';
        }

        return parent::borderless($card);
    }

    public function isExclude(array $json):bool {
        $number = $json["number"];
        return $number >= 252 && $number <= 320;
    }
}
