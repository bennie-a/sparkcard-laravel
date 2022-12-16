<?php
namespace App\Services\json;
use app\Services\json\AbstractCard;

/**
 * 両面カード用クラス
 */
class TransformCard extends AbstractCard {

    public function jpname(string $enname):string
    {
        return $this->getJpnameByAPI($enname);
    }

    public function multiverseId() {
            return $this->json["identifiers"]["multiverseId"];
    }

}
?>