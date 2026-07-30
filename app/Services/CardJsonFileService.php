<?php
namespace App\Services;

use App\Exceptions\api\BadRequestException;
use App\Exceptions\NotFoundException;
use App\Factory\CardInfoFactory;
use App\Models\CardInfo;
use App\Models\Promotype;
use App\Services\Constant\CardConstant as Column;
use App\Services\Constant\GlobalConstant as GCon;
use app\Services\json\AbstractCard;
use BadExpException;

class CardJsonFileService {
    public function build(string $inputSetCode, array $json, bool $isDraft, string $colorFilter) {
        $cards = $json["cards"];
        $setcode = $json["code"];

        if ($inputSetCode != $setcode) {
            throw new BadRequestException('messages.setcode-different');
        }

        $cardInfo = [];
        foreach($cards as $i => $c) {
            $cardtype = CardInfoFactory::create($c);
            if ($cardtype->isExclude()) {
                logger()->debug('skip card:', ['class' => $cardtype::class, Column::NUMBER => $cardtype->number()]);
                continue;
            }

            $enname = $c['name'];
            $promoType = \App\Facades\Promo::find($cardtype);
            $foiltype = $cardtype->foiltype();
            if ($this->isExclude($cardtype, $promoType, $isDraft, $colorFilter)) {
                logger()->debug('skip card:', [GCon::NAME => $cardtype->jpname($enname),
                                                                        Column::NUMBER => $cardtype->number(), Column::PROMOTYPE => $promoType->name]);
                continue;
            }

            $newCard = ['setCode'=> $setcode, GCon::NAME => $cardtype->jpname($enname), "en_name" => $enname,
            'scryfallId' => $cardtype->scryfallId(),
            'color' => $cardtype->color(), Column::NUMBER => $cardtype->number(),
             Column::PROMOTYPE => [GCon::ID=> $promoType->id, GCon::NAME => $promoType->name],
              Column::FOIL_TYPE => $foiltype,
             'isReg' => CardInfo::isExist($setcode, $cardtype->number())];

             if ($cardtype->multiverseId() != 0) {
                $newCard[Column::MULTIVERSEID] = $cardtype->multiverseId();
             }
            logger()->debug(get_class($cardtype).':'.$newCard['name']);

            array_push($cardInfo, $newCard);
            logger()->info('get card:',['name' => $newCard['name'], Column::NUMBER => $newCard[ Column::NUMBER], Column::PROMOTYPE => $newCard[GCon::NAME]]);
        }
        $array = ["setCode"=> $setcode, "cards" => $cardInfo];
        return $array;
    }

    /**
     * 除外カードを判定する。
     *
     * @param array $cardInfo
     * @param array $json
     * @param AbstractCard $cardtype
     * @param string $promo
     * @param boolean $isDraft
     * @param string $colorFilter
     * @return boolean
     */
    private function isExclude(AbstractCard $cardtype, string $promo, bool $isDraft, string $colorFilter) {
        $isExcludeColor = !empty($colorFilter) && $cardtype->color() != $colorFilter;
        if (!$isDraft) {
            return $isExcludeColor;
        }
        $isSpecial = $promo != Promotype::findCardByAttr('draft')->id;
        return $isExcludeColor || $isSpecial;
    }
}
