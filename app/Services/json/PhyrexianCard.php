<?php
namespace App\Services\json;

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
        return parent::getEnScryfallId();
    }

    public function language():string {
        return 'PH';
    }


}
?>