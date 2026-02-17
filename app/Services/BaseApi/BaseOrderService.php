<?php

namespace App\Services\BaseApi;

use App\Factory\GuzzleClientFactory;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class BaseOrderService
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.thebase.in/1/',
            'timeout'  => 10,
        ]);
    }

        /**
     * 認可コードからアクセストークン取得
     *
     */
    public function getAccessToken(string $code)
    {
        try {
                $response = $this->client->post('/1/oauth/token', [
                    'form_params' => [
                        'grant_type'    => 'authorization_code',
                        'client_id'     => config('baseapi.client_id'),
                        'client_secret' => config('baseapi.secret'),
                        'code'          => $code,
                        'redirect_uri'  => config('baseapi.api_url'),
                    ],
                ]);

            return json_decode($response->getBody()->getContents(), true);

        } catch (RequestException $e) {
            throw $e;
        }
    }

    public function fetchOrders(string $accessToken, array $query = [])
    {
        try {
            $response = $this->client->get('/1/orders', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                ],
                'query' => $query,
            ]);
            logger()->debug(current($response->getHeader('Content-Type')));
            return json_decode($response->getBody()->getContents(), true);

        } catch (RequestException $e) {
            throw $e;
        }
    }
}
