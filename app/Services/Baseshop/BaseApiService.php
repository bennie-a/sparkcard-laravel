<?php

namespace App\Services\Baseshop;

use App\Factory\GuzzleClientFactory;
use App\Repositories\Api\Baseshop\BaseApiRepository;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class BaseApiService
{
    protected Client $client;

    private $repo;
    public function __construct(BaseApiRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * BASE APIからアクセストークンとリフレッシュトークンを取得して、DBに登録する。
     *
     * @param string $code
     * @return void
     */
    public function registerToken(string $code) {
        $this->repo->getAccessToken($code);
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
