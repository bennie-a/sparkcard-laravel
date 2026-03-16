<?php
namespace App\Api\Client;
/**
 * 外部APIへの接続設定クラス
 */
abstract class AbstractApiClient
{
    /**
     * APIのベースURLを取得する。
     *
     * @return string APIのベースURL
     */
    abstract public function baseUrl():string;

    public function headers():array
    {
        return [];
    }
}
