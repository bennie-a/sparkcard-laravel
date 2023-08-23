<?php
namespace App\Services;

use App\Enum\CardColor;
use App\Exceptions\NotFoundException;
use App\Factory\CardInfoFactory;
use App\Http\Response\CustomResponse;
use App\Models\Foiltype;
use App\Services\Constant\JsonFileConstant as Column;
use app\Services\json\AbstractCard;

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
            $foiltype = $this->foiltype($cardtype);

            $newCard = ['setCode'=> $setcode, 'name' => $cardtype->jpname($enname), "en_name" => $enname,
            'multiverseId' => $cardtype->multiverseId(), 'scryfallId' => $cardtype->scryfallId(),
            'color' => $color->value, Column::NUMBER => $cardtype->number(),
             Column::PROMOTYPE => $promoType, Column::IS_FOIL => true, 'language' => $cardtype->language(),
             Column::FOIL_TYPE => $foiltype];
            logger()->debug(get_class($cardtype).':'.$newCard['name']);
            
            array_push($cardInfo, $newCard);
            logger()->info('get card:',['name' => $newCard['name'], Column::NUMBER => $newCard[ Column::NUMBER], Column::FOIL_TYPE => $newCard[Column::FOIL_TYPE]]);
        }
        $array = ["setCode"=> $setcode, "cards" => $cardInfo];
        return $array;
    }

    private function foiltype(AbstractCard $cardtype) {
        $foiltype = [];
        if($cardtype->isSpecialFoil()) {
            $type = $cardtype->specialFoil();
            $typename = $this->findFoilName($type);
            array_push($foiltype, $typename);
        } else {
            $finishes = $cardtype->finishes();
            $foiltype = array_map(function($f) {
                if ($f == 'nonfoil') {
                    return '通常版';
                } else if ($f== 'foil') {
                    return 'Foil';
                } else {
                    $typename = $this->findFoilName($f);
                    return $typename;
                }
                }, $finishes);
        }
        return $foiltype;
    }

    private function findFoilName(string $attr) {
        $result = Foiltype::findByAttr($attr);
        $typename = empty($result) ? '不明':$result->name;
        return $typename;
    }
}