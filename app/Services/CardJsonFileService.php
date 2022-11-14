<?php
namespace App\Services;

use App\Factory\CardInfoFactory;

class CardJsonFileService {
    public function build($json) {
        $cards = $json["cards"];
        $setcode = $json["code"];
        $cardInfo = [];
        foreach($cards as $c) {
            $obj = CardInfoFactory::create($c);
            $enname = $c['name'];
            $afterCard = ['name' => $obj->jpname($enname)];
            array_push($cardInfo, $afterCard);
        }
        $array = ["setCode"=> $setcode, "cards" => $cardInfo];
        return $array;
    }
}?>