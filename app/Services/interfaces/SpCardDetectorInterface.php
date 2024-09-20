<?php
namespace App\Services\interfaces;

use App\Services\json\AbstractCard;

/**
 * エキスパンション別に特別版を判定するインターフェイス
 */
interface SpCardDetectorInterface
{
    /**
     * 詳細なショーケースを判別する。
     *
     * @param string $frame jsonのframe_effects
     * @param AbstractCard $card
     * @return string
     */
    public function showcase(string $frame, AbstractCard $card):string;

    public function borderless(AbstractCard $card):string;

    /**
     * ショーケースでもボーダーレスでもない特別版を判定する。
     *
     * @return string
     */
    public function othercase(AbstractCard $card):string;
}