<?php

namespace App\Http\Controllers\DB;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostCardDBRequest;
use App\Http\Resources\CardInfoResource;
use App\Models\Expansion;
use App\Services\CardInfoDBService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
/**
 * card_infoテーブルを操作するAPIクラス
 */
class CardInfoDBController extends Controller
{
    public function __construct(CardInfoDBService $service)
    {
        $this->service = $service;
    }

    /**
     * 検索条件に該当するカード情報を取得する。
     *
     * @param Request $request 検索条件(set,color)
     * @return Response
     */
    public function index(Request $request)
    {
        $condition = $request->only(['set', 'color']);
        logger()->info('search condition:',$condition);
        $result = $this->service->fetch($condition);
        if ($result->isEmpty()) {
            throw new HttpResponseException(response(['message' => '検索結果なし'], Response::HTTP_NO_CONTENT));
        }
        $json = CardInfoResource::collection($result);
        return response($json, Response::HTTP_OK);
        // 検索条件をバリデータ(Set略称：required,半角英数字、色：required, main_colorのキーワード)
        // DBから検索条件に合ったデータを取得する。
        // for文で回してWisdom Guild.netから平均価格を取得する。
        // output⇒name, en_name, exp.name, exp.id, color, price, image_url
    }
    /**
     * card_infoテーブルにデータを1件登録する。
     *
     * @param Request $request
     * @return void
     */
    public function store(PostCardDBRequest $request)
    {
        logger()->info("insert start.");
        $details = $request->all();
        $setCode = $details['setCode'];
        $exp = Expansion::where('attr', $setCode)->get();
        if ($exp->count() == 0) {
            return response($setCode.'がDBに登録されていません', 422);
        }
        // card_infoテーブルに登録
        $this->service->post($exp[0], $details);
        logger()->info("insert end.");
        return response('', Response::HTTP_CREATED);
    }
}
