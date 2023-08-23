<?php
namespace App\Services;

use App\Enum\CardColor;
use App\Exceptions\NotFoundException;
use App\Factory\CardInfoFactory;
use App\Http\Response\CustomResponse;
use App\Models\Foiltype;
use App\Services\Constant\JsonFileConstant as Column;

class CardJsonFileService {
    public function build($json) {
        $cards = $json["cards"];
        $setcode = $json["code"];
        
        // エキスパンション登録チェック
        if (\App\Facades\ExService::isExistByAttr($setcode) == false) {
            throw new NotFoundException(441, $setcode.'が登録されていません。');
        }
        $cardInfo = [];
        foreach($cards as $c) {
            $cardtype = CardInfoFactory::create($c);

            // AbstractCardクラスでスキップ条件メソッドを記述。場合に応じて各カードタイプでメソッド化。
            if ($cardtype->isExclude($c, $cardInfo)) {
                continue;
            }
            $enname = $c['name'];
            $color = CardColor::match($c);
            $promoType = \App\Facades\Promo::find($cardtype);

            $newCard = ['setCode'=> $setcode, 'name' => $cardtype->jpname($enname), "en_name" => $enname,
            'multiverseId' => $cardtype->multiverseId(), 'scryfallId' => $cardtype->scryfallId(),
            'color' => $color->value, Column::NUMBER => $cardtype->number(),
             Column::PROMOTYPE => $promoType, Column::IS_FOIL => true, 'language' => $cardtype->language()];
            logger()->debug(get_class($cardtype).':'.$newCard['name']);

            if ($cardtype->isSpecialFoil()) {
                $newCard[Column::FOIL_TYPE] = $this->foiltype($newCard['name'], $cardtype->specialFoil());
                array_push($cardInfo, $newCard);
                logger()->info('get card:',['name' => $newCard['name'], Column::NUMBER => $newCard[ Column::NUMBER], Column::FOIL_TYPE => $newCard[Column::FOIL_TYPE]]);
            } else {
                foreach($cardtype->finishes() as $f) {
                    $newCard[Column::FOIL_TYPE] = $this->foiltype($newCard['name'], $f);
                    if(strcmp("nonfoil",  $f) == 0) {
                        $newCard[Column::IS_FOIL] = false;
                    } else {
                        $newCard[Column::IS_FOIL] = true;
                    }
                    array_push($cardInfo, $newCard);
                    logger()->info('get card:',['name' => $newCard['name'], Column::NUMBER => $newCard[ Column::NUMBER], Column::FOIL_TYPE => $newCard[Column::FOIL_TYPE]]);
                }
            }
        }
        $array = ["setCode"=> $setcode, "cards" => $cardInfo];
        return $array;
    }

    private function foiltype(string $name, string $attr) {
        if ($attr == 'nonfoil') {
            return '通常版';
        } else if($attr == 'foil') {
            return 'Foil';
        }
        $type = Foiltype::findByAttr($attr);
        if (empty($type)) {
            throw new NotFoundException(CustomResponse::HTTP_NOT_FOUND_FOIL, $name.':'.$attr.'が見つかりません');
        }
        return $type->name;
    }
}