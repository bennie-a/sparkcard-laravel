<?php

namespace App\Repositories\Api\Baseshop;
use App\Factory\GuzzleClientFactory;

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
        logger()->debug("Registering token with code: $code");
        return [];
        // $client = GuzzleClientFactory::createClient(ExternalApi::BASE);
        // try {
        //         $response = $client->post('/1/oauth/token', [
        //             'form_params' => [
        //                 'grant_type'    => 'authorization_code',
        //                 'client_id'     => config('baseapi.client_id'),
        //                 'client_secret' => config('baseapi.secret'),
        //                 'code'          => $code,
        //                 'redirect_uri'  => config('baseapi.api_url'),
        //             ],
        //         ]);

        //     return json_decode($response->getBody()->getContents(), true);

        // } catch (RequestException $e) {
        //     throw $e;
        // }
    }
}
