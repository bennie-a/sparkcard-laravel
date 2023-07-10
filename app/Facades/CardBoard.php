<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Notionの販売管理クラス用のFacadeクラス
 * 処理自体はCardBoardServiceクラスを参照。
 */
class CardBoard extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'CardBoard';
    }
}