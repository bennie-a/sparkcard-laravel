<?php
namespace App\Facades;

use App\Libs\MtgJsonUtil;
use App\Models\Promotype;
use Illuminate\Support\Facades\Facade;

/**
 * Promoクラス用のFacadeクラス
 * 処理自体はPromoServiceクラスを参照。
 */
class Promo extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Promo';
    }
}