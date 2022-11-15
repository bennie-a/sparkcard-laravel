<?php
namespace App\Services\json;

use App\Services\interface\CardInfoInterface;
use App\Services\WisdomGuildService;

/**
 * 日本語が無いカード情報クラス
 */
class NoJpCard extends AbstractCard
{
    public function build(string $enname, $json)
    {
        
    }

    public function jpname(string $enname):string
    {
        $service = new WisdomGuildService();
        $jpname = $service->getJpName($enname);
        return $jpname;
    }

    public function multiverseId()
    {
        return '';
    }

}
?>