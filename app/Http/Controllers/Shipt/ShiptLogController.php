<?php

namespace App\Http\Controllers\Shipt;

use App\Http\Controllers\Controller;
use App\Http\Requests\Shipt\ShiptPostRequest;
use App\Http\Requests\ShiptLogRequest;
use App\Http\Requests\ShowApiRequest;
use App\Http\Resources\Shipt\FetchOrderResource;
use App\Http\Resources\Shipt\OrderResource;
use App\Services\Constant\GlobalConstant as GC;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\Shipt\ShiptLogService;
use App\Services\Constant\ShiptConstant as ShiptCon;
use App\Services\Shipt\ShiptStoreRow;

/**
 * 出荷ログAPI
 */
class ShiptLogController extends Controller
{
    public function __construct(ShiptLogService $service) {
        ini_set("max_execution_time",300); // タイムアウトを240秒にセット
        ini_set("max_input_time",300); // パース時間を240秒にセット

        $this->service = $service;
    }

    /**
     * 出荷手続きを1件行う。
     *
     * @param Request $request
     * @return response
     */
    public function store(ShiptPostRequest $request) {
        $row = $request->only(GC::DATA);
        $log = $this->service->store(new ShiptStoreRow($row[GC::DATA]));
        return response($log, Response::HTTP_CREATED);
    }

    /**
     * 入力した条件に該当する出荷情報を検索する。
     * @param ShiptLogRequest $request
     * @return Response
     */
    public function index(ShiptLogRequest $request) {
        $detail = $request->only([ShiptCon::BUYER, ShiptCon::SHIPT_DATE]);
        $result = $this->service->fetch($detail);
        return response(FetchOrderResource::collection($result), Response::HTTP_OK);
    }

    /**
     * 注文情報IDに該当する出荷情報を1件取得する。
     *
     * @param ShowApiRequest $request
     * @return Response
     *
     */
    public function show(ShowApiRequest $request, int $id) {
        $info = $this->service->show($id);
        return response()->json(new OrderResource($info), Response::HTTP_OK);
    }
}
