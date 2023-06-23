<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Promoクラス用のFacadeクラス
 * 処理自体はPromoServiceクラスを参照。
 */
class Scryfall extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Scryfall';
    }
}