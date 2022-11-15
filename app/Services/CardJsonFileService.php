<?php
namespace App\Services;

use App\Enum\CardColor;
use App\Factory\CardInfoFactory;

class CardJsonFileService {
    public function build($json) {
        $cards = $json["cards"];
        $setcode = $json["code"];
        $cardInfo = [];
        foreach($cards as $c) {
            $obj = CardInfoFactory::create($c);
            $enname = $c['name'];
            $color = CardColor::match($c);
            $afterCard = ['name' => $obj->jpname($enname),"enname" => $enname,
                         'multiverseId' => $obj->multiverseId(), 'scryfallId' => $obj->scryfallId(),
                         'color' => $color->value
                        ];
            array_push($cardInfo, $afterCard);
        }
        $array = ["setCode"=> $setcode, "cards" => $cardInfo];
        return $array;
    }
}?>