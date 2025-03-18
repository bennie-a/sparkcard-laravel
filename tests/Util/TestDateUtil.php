<?php
namespace Tests\Util;
use Carbon\CarbonImmutable;

/**
 * テストで使用する日付を取得する。
 */
class TestDateUtil {
    
    /**
     * 今日の日付を取得する。
     *
     * @return string
     */
    public static  function formatToday():string {
        $today = self::today();
        return self::formatDate($today);
    }

    /**
     * 今日の日付を取得する。
     *
     * @return CarbonImmutable
     */
    public static function today():CarbonImmutable {
        return CarbonImmutable::today();
    }

    public static function formatYesterday() {
        $yesterday = self::yesterday();
        return self::formatDate($yesterday);
    }

    /**
     * 昨日の日付を取得する。
     *
     * @return CarbonImmutable
     */
    public static function yesterday():CarbonImmutable {
        return CarbonImmutable::yesterday();
    }

    public static function formatTwoDateBefore() {
        $two_days_before = self::two_days_before();
        return self::formatDate($two_days_before);
    }

    /**
     * 2日前の日付を取得する。
     *
     * @return CarbonImmutable
     */
    public static function two_days_before():CarbonImmutable {
        return CarbonImmutable::today()->subDays(2);
    }

    /**
     * 3日前の日付を取得する。
     *
     * @return string
     */
    public static function formatThreeDateBefore():string {
        return self::formatDate(self::three_days_before());
    }

    /**
     * 3日前の日付を取得する。
     *
     * @return CarbonImmutable
     */
    public static function three_days_before():CarbonImmutable {
        return CarbonImmutable::today()->subDays(3);
    }

    public static function formatDate(CarbonImmutable $day):string {
        $format = 'Y/m/d';
        return $day->format($format);
    }
}