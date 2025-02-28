<?php
namespace App\Libs;

use Carbon\Carbon;

/**
 * Carbon型のオブジェクトの変換クラス
 */
class CarbonFormatUtil {

    public static function toDateString(string $date):string
    {
        return Carbon::parse($date)->format("Y/m/d");
    }
}