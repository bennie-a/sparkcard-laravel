<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * StockpileServクラス用のFacadeクラス
 * 処理自体はStockpileServiceクラスを参照。
 */
class StockpileServ extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'StockpileServ';
    }
}