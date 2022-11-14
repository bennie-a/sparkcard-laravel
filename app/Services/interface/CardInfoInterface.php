<?php
namespace App\Services\interface;
interface CardInfoInterface
{
    public function build(string $enname, $json);

    public function jpname(string $enname);
}
?>