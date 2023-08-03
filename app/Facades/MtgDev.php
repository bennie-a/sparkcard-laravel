<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * MtgDevクラス用のFacadeクラス
 * 処理自体はMtgDevServiceクラスを参照。
 */
class MtgDev extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'MtgDev';
    }
}