<?php

namespace App\Http\Controllers\Baseshop;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * BASE APIの認証関連クラス
 */
class BaseOAuthController extends Controller
{
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
     * @param Request $request
     * @return void
     */
    public function callback(Request $request)
    {
        $code = $request->query('code');
        return response()->json(['code' => $code]);
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
