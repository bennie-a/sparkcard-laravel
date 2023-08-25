<?php
namespace App\Services\json;

use App\Libs\MtgJsonUtil;
use App\Services\json\AbstractCard;

use function Termwind\ValueObjects\isEmpty;

/**
 * 両面カード用クラス
 */
class TransformCard extends AbstractCard {

    public function jpname(string $enname):string
    {
        return $this->getJpnameByAPI($enname);
    }

    /**
     * multiverse_idを返す
     *@see AbstractCard::multiverseId()
     * @return void
     */
    public function multiverseId() {
            return $this->getEnMultiverseId();
    }

    public function scryfallId()
    {
        return $this->getEnScryfallId();
    }
    /**
     * 除外したいカード情報を検出する。
     * @override AbstractCard
     * @return boolean
     */
    public function isExclude($json, array $cardInfo) {
        if (empty($cardInfo)) {
            return false;
        }
        $name = $json[self::NAME];
        $lastcard = end($cardInfo);
        if (strcmp($name, $lastcard[self::EN_NAME]) == 0) {
            logger()->info('skip transform', ['name' => $name]);
            return true;
        }
        
        return false;
    }
}
?>