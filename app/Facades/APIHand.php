<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * ExServiceクラス用のFacadeクラス
 * 処理自体はExpansionServiceクラスを参照。
 */
class APIHand extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'APIHand';
    }
}