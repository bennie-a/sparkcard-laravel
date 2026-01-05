<?php
namespace App\Libs;

use App\Services\Constant\GlobalConstant;
use Carbon\Carbon;
use Carbon\CarbonImmutable;

/**
 * Carbon型のオブジェクトの変換クラス
 */
class CarbonFormatUtil {

    public static function toDateString(string $date):string
    {
        return Carbon::parse($date)->format("Y/m/d");
    }

    /**
     * 日付が空の場合は今日の日付を取得する。
     *
     * @param string $date
     * @return string
     */
    public static function assignTodayIfMissing(string $date):string {
        if (empty($date)) {
            return self::today();
        }
        return $date;
    }

    /**
     * 今日の日付を取得する。
     *
     * @return string
     */
    public static function today():string {
        $today = CarbonImmutable::today();
        return $today->format(GlobalConstant::DATE_FORMAT);
    }
}
