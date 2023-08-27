<?php
namespace App\Services\json;
use App\Services\Constant\JsonFileConstant as Con;
/**
 * 初期セットに収録されているカード情報
 */
class StarterCard extends AbstractCard {

    public function multiverseId()
    {
        return $this->json['identifiers']['multiverseId'];
    }
}
?>