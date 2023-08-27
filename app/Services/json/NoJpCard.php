<?php
namespace App\Services\json;

use App\Models\CardInfo;

/**
 * 日本語情報が無いカード情報クラス
 * @deprecated 3.2.0
 */
class NoJpCard extends AbstractCard
{

    public function number()
    {
        $number = parent::number();
        return preg_replace('/[a-zA-Z]/', '', $number);
    }

    public function scryfallId()
    {
        return $this->getJson()['identifiers']['scryfallId'];
    }
}
?>