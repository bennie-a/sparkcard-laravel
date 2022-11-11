<?php
namespace App\Services;
class CardJsonFileService {
    public function build($setCode, $cards) {
        $array = ["setCode"=> $setCode, "cards"=>["aaa" => 1, 'bbb' => 2]];
        return $array;
    }
}?>