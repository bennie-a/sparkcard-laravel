<?php
namespace App\Services\json;

/**
 * フルアート版基本土地クラス
 */
class FullartLand extends AbstractCard
{
    public function jpname(string $enname):string
    {
        $names = ["Island" => "島", "Plains" => "平地", "Swamp" => "沼", "Forest" => "森", "Mountain" => "山"];
        return $names[$enname];
    }

    public function multiverseId()
    {
        return $this->json["identifiers"]["multiverseId"];
    }

    public function scryfallId()
    {
        return '';
    }
}
?>