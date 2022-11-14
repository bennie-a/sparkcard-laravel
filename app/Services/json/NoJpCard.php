<?php
namespace App\Services\json;

use App\Services\interface\CardInfoInterface;

class NoJpCard extends AbstractCard
{
    public function build(string $enname, $json)
    {
        
    }

    public function jpname(string $enname)
    {
        return '記載なし';
    }
}
?>