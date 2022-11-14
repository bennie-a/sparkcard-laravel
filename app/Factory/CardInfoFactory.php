<?php
namespace App\Factory;

use App\Services\json\JpCard;
use App\Services\json\JpLimitedCard;
use App\Services\json\NoJpCard;

class CardInfoFactory {

    public static function create($json) {
        $enname = $json['name'];
        $lang = $json['language'];
        if ($lang == 'Japanese') {
            return new JpLimitedCard($json);
        }
        $isForeign = array_key_exists('foreignData', $json);
        if ($isForeign) {
            return new JpCard($json);
        }
        return new NoJpCard($json);
    }
}
?>