<?php
namespace App\Services\json;

use App\Services\WisdomGuildService;

/**
 * ザ・リストに収録されているカード
 */
class PlistCard extends AbstractCard
{
    public function multiverseId()
    {
        return '';
    }

    public function jpname(string $enname): string
    {
        return $this->getJpnameByAPI($enname);
    }

    public function scryfallId()
    {
        return $this->json['identifiers']['scryfallId'];
    }

}
?>