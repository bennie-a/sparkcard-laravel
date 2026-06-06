<?php
namespace App\Services\Specific;

use App\Services\json\AbstractCard;

/**
 * 『マーベル スーパー・ヒーローズ[MSH]』の特別版
 * を判別するクラス
 */
class MshSpCardDetector extends DefaultSpCardDetector {
    public function borderless(AbstractCard $card):string {
        $number = $card->number();
        // パネルカード
        if ($number >= 297 && $number <= 313) {
            return 'postercard';
        }
        // 計略カード
        if ($number >= 314 && $number <= 351) {
            return 'strategy';
        }

        // ロゴカード
        if ($number >= 352 && $number <= 379) {
            return 'logo';
        }
        return parent::borderless($card);
    }
}
