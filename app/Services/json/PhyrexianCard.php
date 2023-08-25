<?php
namespace App\Services\json;

use App\Services\WisdomGuildService;

/**
 * 言語がファイレクシア語版のカード
 */
class PhyrexianCard extends AbstractCard
{
    public function jpname(string $enname): string
    {
        $jpname = $this->getJpnameByAPI($enname);
        return $jpname;
    }

    public function scryfallId()
    {
        return $this->json['identifiers']['scryfallId'];
    }

    public function language():string {
        return 'PH';
    }


}
?>