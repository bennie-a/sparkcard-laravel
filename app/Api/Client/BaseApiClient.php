<?php

/**
 * BASE APIの接続設定クラス
 */
class BaseApiClient extends AbstractApiClient
{
    public function baseUrl(): string
    {
        return 'https://api.thebase.in/1/';
    }

    public function headers(): array
    {
        return [
            // 'Authorization' => 'Bearer ' . config('baseapi.access_token'),
            'Content-Type' => 'application/json',
        ];
    }
}
