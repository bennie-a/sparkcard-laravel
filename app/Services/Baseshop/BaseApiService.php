<?php

namespace App\Services\Baseshop;

use App\Repositories\Api\Baseshop\BaseApiRepositoryInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class BaseApiService
{
    protected Client $client;

    private $repo;
    public function __construct(BaseApiRepositoryInterface $repo)
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
        try {
            $tokens = $this->repo->getAccessToken($code);
            logger()->debug('取得したトークン', $tokens);
            // トークンをDBに保存する処理をここに追加
            $this->repo->registToken($tokens);
        } catch (RequestException $e) {
            throw $e;
        }
        return true;
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
