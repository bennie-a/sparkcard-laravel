<?php
namespace App\Services\json;

use App\Services\WisdomGuildService;

/**
 * 言語がファイレクシア語版のカード
 */
class PhyrexianCard extends AbstractCard
{
    public function multiverseId()
    {
        return '';
    }

    public function jpname(string $enname): string
    {
        $service = new WisdomGuildService();
        $jpname = $service->getJpName($enname);
        return $jpname."[ファイレクシア語版]";
    }

    public function scryfallId()
    {
        return $this->json['identifiers']['scryfallId'];
    }

}
?>