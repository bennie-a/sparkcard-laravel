<?php
namespace App\Services\json;

use App\Libs\MtgJsonUtil;
use App\Services\Constant\CardConstant as Con;


class JsonCard extends AbstractCard
{

    /**
     * multiverse_idを返す
     * @see AbstractCard::multiverseId()
     * @return int
     */
    public function multiverseId()
    {
        return MtgJsonUtil::hasKey(Con::MULTIVERSEID, $this->jp) ?
                 $this->jp[Con::MULTIVERSEID] : parent::getEnMultiverseId();
    }

    /**
     * scryfall_id_idを返す
     * @see AbstractCard::scryfallId()
     * @return string
     */
    public function scryfallId()
    {
        return MtgJsonUtil::hasKey(Con::SCRYFALLID, $this->jp) ?
                 $this->jp[Con::SCRYFALLID] : parent::getEnScryfallId();
    }

}
?>