<?php
namespace App\Services\json;
/**
 * 対象外カード。このクラスのカード情報は返さない。
 */
class ExcludeCard extends AbstractCard
{

    public function multiverseId()
    {
        return '';
    }

    /**
     * 除外したいカード情報の条件式
     * @override AbstractCard
     * @return boolean
     */
    public function isExclude($json, array $cardInfo) {
        return true;
    }

}
?>