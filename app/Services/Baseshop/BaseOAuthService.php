<?php

namespace App\Services\Baseshop;

use App\Repositories\Api\Baseshop\BaseOAuthRepository;
use Carbon\CarbonImmutable;
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
    public function registToken(string $code) {
        $tokens = $this->repo->getAccessToken($code);
        logger()->debug('取得したトークン', $tokens);
        // トークンをDBに保存する処理をここに追加
        $this->repo->registToken($tokens);
        return true;
    }

    /**
     * BASE APIと連携済みか検証する。
     *
     * @return boolean
     */
    public function isConnected() {
        $record = $this->repo->getLatestToken();
        // トークンが未登録の場合は連携していないとみなす。
        if (!$record) {
            return false;
        }
        $now = CarbonImmutable::now();
        $diff = $record->expires_in->diffInMinutes($now);

        if ($diff <= 60) {
            return true;
        }

        $refreshToken = $record->refresh_token;
        $tokens = $this->repo->refreshAccessToken($refreshToken);
        $this->repo->updateToken($record, $tokens);
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
