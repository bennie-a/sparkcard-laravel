<?php

namespace App\Http\Controllers\Baseshop;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\BaseCallbackRequest;
use App\Services\Baseshop\BaseApiService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\Constant\BaseApiConstant as BCon;

/**
 * BASE APIの認証関連クラス
 */
class BaseOAuthController extends Controller
{

    private $service;
    public function __construct(BaseApiService $service)
    {
        $this->service = $service;
    }
    /**
     * 認可サーバーへのURLを取得する。
     *
     * @return Response
     */
    public function connect()
    {
        $client_id = config('baseapi.client_id');
        $app_url = config('baseapi.api_url');
        $redirect_url = 'https://api.thebase.in/1/oauth/authorize?response_type=code&';
        $redirect_url .= "client_id=$client_id&redirect_uri=$app_url&scope=read_items%20read_orders";
        return response()->json(['url' => $redirect_url], Response::HTTP_OK);
    }

    /**
     * BASE APIからアクセストークンとリフレッシュトークンを登録する。
     *
     * @param BaseCallbackRequest $request
     * @return Response
     */
    public function callback(BaseCallbackRequest $request)
    {
        $code = $request->input(BCon::AUTH_CODE);
        $isConnected = $this->service->registerToken($code);
        return response()->json(['connected' => $isConnected], Response::HTTP_CREATED);
    }

    /**
     * BASE APIと連携済みか確認する。
     *
     * @return void
     */
    public function status()
    {

    }
}
