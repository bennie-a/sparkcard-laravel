<?php
namespace App\Libs;
class MtgJsonUtil {

    /**
     * JSONファイルの項目がtrueか判別する。
     *
     * @param string $key
     * @param array $json
     * @return boolean
     */
    public static function isTrue($key, $json) {
        if (!array_key_exists($key, $json)) {
            return false;
        }
        return $json[$key] == 'true';
    }

    public static function hasKey($key, $json) {
        if (empty($json)) {
            return false;
        }
        return isset($json[$key]);
    }

    public static function isEmpty($key, $json) {
        return !self::isNotEmpty($key, $json);
    }

    /**
     * 配列にキーが存在すれば該当する値を、なければnullを取得する。
     *
     * @param string $key
     * @param array $json
     * @return string
     */
    public static function getIfExists($key, $json) {
        $isTrue = self::hasKey($key, $json);
        return $isTrue ? $json[$key] : null;
    }

    /**
     * JSONファイルの項目が空ではないか判別する。
     *
     * @param string $key
     * @param array $json
     * @return boolean
     */
    public static function isNotEmpty($key, $json) {
        return self::hasKey($key, $json) && !empty($json[$key]);
    }

    public static function extractJp($json) {
        $target = array_filter($json, function($f) {
            return $f['language'] == 'Japanese';
        });
        return $target;
    }
}
?>