<?php
namespace App\Services\json;

/**
 * 日本語が無いカード情報クラス
 */
class NoJpCard extends AbstractCard
{
    public function jpname(string $enname):string
    {
        return $this->getJpnameByAPI($enname);
    }

    public function multiverseId()
    {
        return '';
    }

    public function number()
    {
        $number = parent::number();
        return preg_replace('/[a-zA-Z]/', '', $number);
    }

    public function scryfallId()
    {
        return $this->json['identifiers']['scryfallId'];
    }
}
?>