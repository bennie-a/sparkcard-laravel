<?php
namespace App\Services\json;
/**
 * 対象外カード。このクラスのカード情報は返さない。
 */
class ExcludeCard extends AbstractCard
{

    /**
     * 除外したいカード情報の条件式
     * @override AbstractCard
     * @return boolean
     */
    public function isExclude() {
        return true;
    }

}
?>