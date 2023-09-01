<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * CardInfoDBServiceクラス用のFacadeクラス
 * 処理自体はCardInfoDBServiceクラスを参照。
 */
class CardInfoServ extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'CardInfoServ';
    }
}