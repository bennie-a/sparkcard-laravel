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
        $params = $this->getClientInfo('authorization_code');
        $params['code'] = $code;
        return $this->getToken($params);
    }

    /**
     * アクセストークンを再発行する。
     *
     * @param string $refreshToken
     * @return array
     */
    public function refreshAccessToken(string $refreshToken):array
    {
        $params = $this->getClientInfo('refresh_token');
        $params['refresh_token'] = $refreshToken;
        return $this->getToken($params);
    }

    /**
     * リクエストパラメータをもとに
     * アクセストークンとリフレッシュトークンを取得する。
     *
     * @param array $params リクエストパラメータ
     * @return array
     */
    private function getToken(array $params) {
        try {
            $client = GuzzleClientFactory::createClient(ExternalApi::BASE);
            $response = $client->post('/1/oauth/token', [
                'form_params' => $params,
            ]);

            return json_decode($response->getBody()->getContents(), true);

        } catch (RequestException $e) {
            $response  = $e->getResponse();
            $contents = $response->getBody()->getContents();

            throw new BaseApiException($contents);
        }
    }

    /**
     * アクセストークン取得に必要なリクエストパラメータを取得する。
     *
     * @param string $grantType
     * @return array リクエストパラメータ
     */
    private function getClientInfo(string $grantType):array {
        $params = [
                'grant_type'    => $grantType,
                'client_id'     => config('baseapi.client_id'),
                'client_secret' => config('baseapi.secret'),
                'redirect_uri'  => config('baseapi.api_url'),
            ];
        return $params;
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

    /**
     * BASE APIのトークンを更新する。
     *
     * @param BaseToken $record
     * @param array $tokens
     * @return void
     */
    public function updateToken(BaseToken $record, array $tokens) {
        $record->update([
            BCon::ACCESS_TOKEN => $tokens[BCon::ACCESS_TOKEN],
            BCon::REFRESH_TOKEN => $tokens[BCon::REFRESH_TOKEN],
            BCon::EXPIRES_IN => now()->addSeconds($tokens[BCon::EXPIRES_IN])
        ]);
    }

    /**
     * 有効期限が最新のアクセストークンを1件取得する。
     *
     * @return BaseToken
     */
    public function getLatestToken() {
        $record = BaseToken::fetchLastRecord();
        return $record;
    }
}
