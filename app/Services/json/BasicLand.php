<?php
namespace App\Services\json;
use App\Services\Constant\JsonFileConstant as Con;
/**
 * 基本土地クラス
 */
class BasicLand extends JsonCard
{
    public function jpname(string $enname):string
    {
        $names = ["Island" => "島", "Plains" => "平地", "Swamp" => "沼", "Forest" => "森", "Mountain" => "山"];
        return $names[$enname].'('.$this->number().')';
    }
}
?>