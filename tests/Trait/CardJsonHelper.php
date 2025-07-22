<?php

namespace Tests\Trait;

use App\Enum\CardColor;
use App\Models\CardInfo;
use App\Services\Constant\CardConstant as CCon;
use App\Services\Constant\GlobalConstant as GCon;
use App\Services\Constant\StockpileHeader as Header;
/**
 * card要素を検証するtrait
 */
trait CardJsonHelper
{
    protected function buildCardJson(CardInfo $card): array
    {
        return [
            GCon::ID => $card->id,
            GCon::NAME => $card->name,
            CCon::EXP => [
                GCon::NAME => $card->exp_name,
                CCon::ATTR => $card->exp_attr,
            ],
            CCon::NUMBER => $card->number,
            CCon::COLOR => CardColor::tryFrom($card->color_id)->text(),
            CCon::IMAGE_URL => $card->image_url,
            'foil' => [
                'is_foil' => $card->isFoil,
                GCon::NAME => $card->foiltype == '通常版' ? '' : $card->foiltype
            ],
            CCon::PROMOTYPE => [
                GCon::ID => $card->promotype_id,
                GCon::NAME => $card->promo_name,
            ],
        ];
    }
}
