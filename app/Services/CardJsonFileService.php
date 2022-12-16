<?php
namespace App\Services;

use App\Enum\CardColor;
use App\Enum\PromoType;
use App\Factory\CardInfoFactory;
use app\Libs\JsonUtil;
use App\Services\json\ExcludeCard;

class CardJsonFileService {
    public function build($json) {
        $cards = $json["cards"];
        $setcode = $json["code"];
        $cardInfo = [];
        foreach($cards as $c) {
            $obj = CardInfoFactory::create($c);
            if ($obj instanceof ExcludeCard) {
                continue;
            }
            $enname = $c['name'];
            $color = CardColor::match($c);
            $promo = PromoType::match($c);
            $newCard = ['setCode'=> $setcode, 'name' => $obj->jpname($enname),"en_name" => $enname,
            'multiverseId' => $obj->multiverseId(), 'scryfallId' => $obj->scryfallId(),
            'color' => $color->value, 'number' => $obj->number(), 'promotype' => $promo->text(), 'isFoil' => false,
            'language' => $obj->language()
            ];
            logger()->info('get card:'.$newCard['name']);
            logger()->debug(get_class($obj).':'.$newCard['name']);
            array_push($cardInfo, $newCard);
            if ($c['hasFoil']) {
                $newCard['isFoil'] = true;
                array_push($cardInfo, $newCard);
            }
        }
        $array = ["setCode"=> $setcode, "cards" => $cardInfo];
        return $array;
    }

    private static function isTrue($key, $json) {
        if (!array_key_exists($key, $json)) {
            return false;
        }
        return $json[$key] == 'true';
    }

}?>