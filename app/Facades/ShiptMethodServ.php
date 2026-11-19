<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * ShiptMethodServクラス用のFacadeクラス
 * 処理自体はShiptMethodServiceクラスを参照。
 */
class ShiptMethodServ extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'ShiptMethodServ';
    }
}
