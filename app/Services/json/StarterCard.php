<?php
namespace App\Services\json;
/**
 * 初期セットに収録されているカード情報
 */
class StarterCard extends AbstractCard {

    public function multiverseId()
    {
        return parent::getEnMultiverseId();
    }
}
?>