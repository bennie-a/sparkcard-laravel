<?php
namespace App\Services;

use App\Enum\CardColor;
use App\Enum\PromoType;
use App\Factory\CardInfoFactory;
use App\Services\json\OnlineCard;

class CardJsonFileService {
    public function build($json) {
        $cards = $json["cards"];
        $setcode = $json["code"];
        $cardInfo = [];
        foreach($cards as $c) {
            $obj = CardInfoFactory::create($c);
            if ($obj instanceof OnlineCard) {
                continue;
            }
            $enname = $c['name'];
            $color = CardColor::match($c);
            $promo = PromoType::match($c);
            $afterCard = ['setCode'=> $setcode, 'name' => $obj->jpname($enname),"en_name" => $enname,
            'multiverseId' => $obj->multiverseId(), 'scryfallId' => $obj->scryfallId(),
            'color' => $color->value, 'number' => $obj->number(), 'promotype' => $promo->text()
            ];
            logger()->info('get card:'.$afterCard['name']);
            array_push($cardInfo, $afterCard);
        }
        $array = ["setCode"=> $setcode, "cards" => $cardInfo];
        return $array;
    }
}?>