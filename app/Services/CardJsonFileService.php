<?php
namespace App\Services;

use App\Enum\CardColor;
use App\Enum\PromoType;
use App\Exceptions\NoPromoTypeException;
use App\Factory\CardInfoFactory;
use app\Libs\JsonUtil;
use App\Libs\MtgJsonUtil;
use App\Services\json\ExcludeCard;
use App\Services\json\TransformCard;

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

            // 両面カード対応
            $enname = $c['name'];
            if ($obj instanceof TransformCard) {
                $lastcard = end($cardInfo);
                if (strcmp($enname, $lastcard['en_name']) == 0) {
                    logger()->info('skip transform', ['name' => $enname]);
                    continue;
                }
            }
            $color = CardColor::match($c);
            $promo = PromoType::match($c);
            if ($promo == PromoType::OTHER) {
                throw new NoPromoTypeException($obj->jpname($enname), $obj->number());
            }

            $newCard = ['setCode'=> $setcode, 'name' => $obj->jpname($enname),"en_name" => $enname,
            'multiverseId' => $obj->multiverseId(), 'scryfallId' => $obj->scryfallId(),
            'color' => $color->value, 'number' => $obj->number(), 'promotype' => $promo->text(), 'isFoil' => false,
            'language' => $obj->language()
            ];
            logger()->debug(get_class($obj).':'.$newCard['name']);
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