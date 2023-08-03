<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * ScryfallServクラス用のFacadeクラス
 * 処理自体はScryfallServiceクラスを参照。
 */
class ScryfallServ extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'ScryfallServ';
    }
}