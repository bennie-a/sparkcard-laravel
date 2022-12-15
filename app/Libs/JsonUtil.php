<?php
namespace app\Libs;
class JsonUtil {

    public static function isTrue($key, $json) {
        if (!array_key_exists($key, $json)) {
            return false;
        }
        return $json[$key] == 'true';
    }

    public static function hasKey($key, $json) {
        return array_key_exists($key, $json);
    }

    public static function isNotEmpty($key, $json) {
        return hasKey($key, $json) && !empty($json[$key]);
    }
}
?>