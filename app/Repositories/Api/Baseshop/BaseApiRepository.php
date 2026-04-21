<?php

namespace App\Repositories\Api\Baseshop;

use App\Enum\ExternalApi;
use App\Factory\GuzzleClientFactory;
use App\Models\BaseToken;

/**
 * BASE APIとの連携クラス
 */
class BaseApiRepository implements BaseApiRepositoryInterface
{

    /**
     * 認可コードからアクセストークンを取得する。
     *
     * @param string $code
     * @return array
     */
    public function getAccessToken(string $code):array
    {
        try {
            GuzzleClientFactory::createClient(ExternalApi::BASE);
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

    /**
     * BASE APIのトークンを登録する。
     *
     * @param array $tokens
     * @return void
     */
    public function registToken(array $tokens)
    {
        BaseToken::create([
            'access_token' => $tokens['access_token'],
            'refresh_token' => $tokens['refresh_token'],
            'expires_at' => now()->addSeconds($tokens['expires_in'])
        ]);
    }
}
