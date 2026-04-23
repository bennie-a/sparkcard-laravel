<?php

namespace App\Repositories\Api\Baseshop;

use App\Enum\ExternalApi;
use App\Exceptions\api\Baseshop\BaseApiException;
use App\Factory\GuzzleClientFactory;
use App\Models\BaseToken;
use GuzzleHttp\Exception\RequestException;
use App\Services\Constant\BaseApiConstant as BCon;

/**
 * BASE APIの認証関連のリポジトリクラス
 */
class BaseOAuthRepository
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
            $client = GuzzleClientFactory::createClient(ExternalApi::BASE);
            $response = $client->post('/1/oauth/token', [
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
            $response  = $e->getResponse();
            $contents = $response->getBody()->getContents();

            throw new BaseApiException($contents);
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
            BCon::ACCESS_TOKEN => $tokens[BCon::ACCESS_TOKEN],
            BCon::REFRESH_TOKEN => $tokens[BCon::REFRESH_TOKEN],
            BCon::EXPIRES_IN => now()->addSeconds($tokens[BCon::EXPIRES_IN])
        ]);
    }
}
