<?php

namespace App\Http\Controllers;

use App\Exceptions\Api\Csv\CsvInvalidRowException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Shipt\ShiptPostRequest;
use App\Http\Requests\Shipt\ShiptStoreRequest;
use App\Http\Requests\Shipt\ShiptUploadRequest;
use App\Http\Requests\ShiptLogRequest;
use App\Http\Resources\Shipt\OrderResource;
use App\Services\Constant\GlobalConstant as GC;
use App\Traits\ImportCsv;
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
        return response([ShiptCon::ORDER_ID => $log->order_id,
                                         GC::CREATE_AT => $log->created_at], Response::HTTP_CREATED);
    }

    use ImportCsv;

    /**
     * 入力した条件に該当する出荷情報を検索する。
     * @param ShiptLogRequest $request
     * @return Response
     */
    public function index(ShiptLogRequest $request) {
        $detail = $request->only([ShiptCon::BUYER, ShiptCon::SHIPPING_DATE]);
        $result = $this->service->fetch($detail);
        logger()->debug($request);
        logger()->info($result);
        return response()->json($result, Response::HTTP_OK);
    }

    /**
     * 出荷IDに該当する出荷情報を1件取得する。
     *
     * @param string $orderId 注文番号
     * @return Response
     */
    public function show(string $orderId) {
        $info = $this->service->show($orderId);
        return response()->json($info, Response::HTTP_OK);
    }

    /**
     * 注文CSVファイルを解析する。
     *
     * @param ShiptUploadRequest $request
     * @return void
     */
    public function parse(ShiptUploadRequest $request) {
        $data = $request->input(GC::DATA);
        $records = $this->service->parse($data);
        if ($this->service->hasError()) {
            throw new CsvInvalidRowException($this->service->getError());
        }

        return response(OrderResource::collection($records), Response::HTTP_CREATED);
    }

}
