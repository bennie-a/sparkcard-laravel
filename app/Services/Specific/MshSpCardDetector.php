<?php
namespace App\Services\Specific;

use App\Services\json\AbstractCard;
use Override;

/**
 * 『マーベル スーパー・ヒーローズ[MSH]』の特別版
 * を判別するクラス
 */
class MshSpCardDetector extends DefaultSpCardDetector {

    #[Override]
    public function showcase(string $frame, AbstractCard $card): string
    {
        $number = $card->number();
        // パネルカード
        if ($number >= 297 && $number <= 313) {
            return 'poster';
        }
        return parent::showcase($frame, $card);
    }
    public function borderless(AbstractCard $card):string {
        $number = $card->number();
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
