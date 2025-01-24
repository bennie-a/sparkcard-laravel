<?php

namespace App\Http\Controllers\DB;

use App\Exceptions\api\NoContentException;
use App\Http\Controllers\Controller;
use App\Http\Requests\CsvFileRequest;
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
    private CardInfoDBService $service;

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
        ini_set("max_execution_time",180); // タイムアウトを180秒にセット
        ini_set("max_input_time",180); // パース時間を180秒にセット

        $condition = $request->only(['name', 'set', 'color', 'isFoil']);
        logger()->info('search condition:',$condition);
        $result = $this->service->fetch($condition);
        if ($result->isEmpty()) {
            throw new NoContentException();
        }
        $json = CardInfoResource::collection($result);
        logger()->info('search finished.');
        return response($json, Response::HTTP_OK);
    }
    /**
     * card_infoテーブルにデータを1件登録する。
     *
     * @param Request $request
     * @return void
     */
    public function store(PostCardDBRequest $request)
    {
        $details = $request->all();
        $setCode = $details['setCode'];
        // card_infoテーブルに登録
        $this->service->post($setCode, $details);
        return response('', Response::HTTP_CREATED);
    }
}
