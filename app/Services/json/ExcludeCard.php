<?php
namespace App\Services\json;
/**
 * オンラインのみ使用するカード。このクラスのカードは対象外。
 */
class ExcludeCard extends AbstractCard
{

    public function multiverseId()
    {
        return '';
    }
}
?>