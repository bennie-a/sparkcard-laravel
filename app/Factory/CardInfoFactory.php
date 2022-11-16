<?php
namespace App\Factory;

use App\Services\json\JpCard;
use App\Services\json\JpLimitedCard;
use App\Services\json\NoJpCard;
use App\Services\json\OnlineCard;

class CardInfoFactory {

    public static function create($json) {
        if (array_key_exists('isOnlineOnly', $json) && $json['isOnlineOnly'] == 'true') {
            return new OnlineCard($json);
        }
        $enname = $json['name'];
        $lang = $json['language'];
        if ($lang == 'Japanese') {
            return new JpLimitedCard($json);
        }
        $isForeign = array_key_exists('foreignData', $json);
        if ($isForeign && empty($json['foreignData']) == false) {
            return new JpCard($json);
        }
        return new NoJpCard($json);
    }
}
?>