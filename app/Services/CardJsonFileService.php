<?php
namespace App\Services;

use App\Exceptions\NotFoundException;
use App\Factory\CardInfoFactory;
use App\Services\Constant\CardConstant as Column;
use app\Services\json\AbstractCard;

class CardJsonFileService {
    public function build($json, bool $isDraft, string $colorFilter) {
        $cards = $json["cards"];
        $setcode = $json["code"];
        
        // エキスパンション登録チェック
        if (\App\Facades\ExService::isExistByAttr($setcode) == false) {
            throw new NotFoundException(441, $setcode.'が登録されていません。');
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
                logger()->debug('skip card:', [Column::NAME => $cardtype->jpname($enname), 
                                                                        Column::NUMBER => $cardtype->number(), Column::PROMOTYPE => $promoType]);
                continue;
            }

            $newCard = ['setCode'=> $setcode, 'name' => $cardtype->jpname($enname), "en_name" => $enname,
            'multiverseId' => $cardtype->multiverseId(), 'scryfallId' => $cardtype->scryfallId(),
            'color' => $cardtype->color(), Column::NUMBER => $cardtype->number(),
             Column::PROMOTYPE => $promoType, 'language' => $cardtype->language(),
             Column::FOIL_TYPE => $foiltype];
            logger()->debug(get_class($cardtype).':'.$newCard['name']);
            
            array_push($cardInfo, $newCard);
            logger()->info('get card:',['name' => $newCard['name'], Column::NUMBER => $newCard[ Column::NUMBER], Column::PROMOTYPE => $newCard[Column::PROMOTYPE]]);
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
        return $isExcludeColor || $isDraft && !empty($promo);
    }
}