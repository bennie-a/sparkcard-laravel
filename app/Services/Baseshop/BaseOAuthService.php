<?php

namespace App\Services\Baseshop;

use App\Repositories\Api\Baseshop\BaseOAuthRepository;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class BaseOAuthService
{
    protected Client $client;

    private $repo;
    public function __construct(BaseOAuthRepository $repo)
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
        $tokens = $this->repo->getAccessToken($code);
        logger()->debug('取得したトークン', $tokens);
        // トークンをDBに保存する処理をここに追加
        $this->repo->registToken($tokens);
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
