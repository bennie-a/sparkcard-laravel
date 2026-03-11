<?php

namespace App\Api\Client;
/**
 * Scryfall APIの接続設定クラス
 */
class ScryfallClient extends AbstractApiClient
{
    public function baseUrl():string
    {
        return "https://api.scryfall.com/";
    }

    public function headers():array
    {
        return [
             'User-Agent' => 'MyMTGApp/1.0 (beninekoyamtg@gmail.com)',
            'Accept' => 'application/json;q=0.9,*/*;q=0.8'
        ];
    }
}
