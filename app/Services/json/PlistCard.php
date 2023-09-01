<?php
namespace App\Services\json;

use App\Services\WisdomGuildService;

/**
 * ザ・リストに収録されているカード
 */
class PlistCard extends AbstractCard
{
    public function jpname(string $enname): string
    {
        return $this->getJpnameByAPI($enname);
    }

    public function scryfallId()
    {
        return parent::getEnScryfallId();
    }

}
?>