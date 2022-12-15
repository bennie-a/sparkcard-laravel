<?php
namespace App\Services\json;

/**
 * 初期セットに収録されているカード情報
 */
class StarterCard extends AbstractCard {
    public function __construct($json)
    {
        parent::__construct($json);
        $this->jp = $this->getJp($json);
    }

    public function multiverseId()
    {
        return $this->json['identifiers']['multiverseId'];
    }
}
?>