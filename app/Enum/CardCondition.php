<?php
namespace App\Enum;

/** カードの状態に関するenum */
enum CardCondition:string {
    case NM = 'NM';
    case NM_MINUS = 'NM-';
    case EX_PLUS = 'EX+';
    case EX = 'EX';
    case PLD = 'PLD';
    case UNDEFINED = 'UNDEFINED';

    public static function find(string $condition) {
        return CardCondition::tryFrom($condition) ?? CardCondition::UNDEFINED;
    }
}
