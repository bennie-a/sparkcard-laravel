<?php
namespace App\Services;

use App\Enum\CardColor;
use App\Enum\PromoTypeEnum;
use App\Exceptions\NoPromoTypeException;
use App\Factory\CardInfoFactory;
use app\Libs\JsonUtil;
use App\Libs\MtgJsonUtil;

class CardJsonFileService {
    public function build($json) {
        $cards = $json["cards"];
        $setcode = $json["code"];
        $cardInfo = [];
        foreach($cards as $c) {
            $cardtype = CardInfoFactory::create($c);

            // AbstractCardクラスでスキップ条件メソッドを記述。場合に応じて各カードタイプでメソッド化。
            if ($cardtype->isExclude($c, $cardInfo)) {
                continue;
            }
            // 両面カード対応
            $enname = $c['name'];
            $color = CardColor::match($c);
            $promo = PromoTypeEnum::match($c);
            if ($promo == PromoTypeEnum::OTHER) {
                throw new NoPromoTypeException($cardtype->jpname($enname), $cardtype->number());
            }

            $newCard = ['setCode'=> $setcode, 'name' => $cardtype->jpname($enname),"en_name" => $enname,
            'multiverseId' => $cardtype->multiverseId(), 'scryfallId' => $cardtype->scryfallId(),
            'color' => $color->value, 'number' => $cardtype->number(), 'promotype' => $promo->text(), 'isFoil' => false,
            'language' => $cardtype->language()
            ];
            logger()->debug(get_class($cardtype).':'.$newCard['name']);
            if (!MtgJsonUtil::hasKey('hasNonFoil', $c) || (MtgJsonUtil::hasKey('hasNonFoil', $c) && $c['hasNonFoil'] == true)) {
                array_push($cardInfo, $newCard);
            }
            if ($c['hasFoil']) {
                $newCard['isFoil'] = true;
                array_push($cardInfo, $newCard);
            }
            logger()->info('get card:',['name' => $newCard['name'], 'number' => $newCard['number'], 'isFoil' => $newCard['isFoil']]);
        }
        $array = ["setCode"=> $setcode, "cards" => $cardInfo];
        return $array;
    }

}?>