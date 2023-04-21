<?php
namespace App\Services\interfaces;
/**
 * JSONファイルのカード情報に関するインターフェイス
 */
interface CardInfoInterface
{
    /**
     * 英語名から日本語名を取得する。
     *
     * @param string $enname 英語名
     * @return string 日本語名
     */
    public function jpname(string $enname):string;

    public function multiverseId();

    public function scryfallId();

    public function number();

    public function language():string;
}
?>