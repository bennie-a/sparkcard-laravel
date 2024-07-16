<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShiptLogRequest;
use App\Services\Stock\ShippingLogService;
use App\Traits\ImportCsv;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\Constant\StockpileHeader as Header;


/**
 * 出荷ログAPI
 */
class ShippingLogController extends Controller
{
    public function __construct(ShippingLogService $service) {
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
    public function store(Request $request) {
        /**
         * 在庫情報を確認する。
         * ⇒ある⇒出荷ログを作成する
         * ⇒ない⇒エラー
         * 在庫情報の数量から出荷量を引く。
         * Notionのカードに発送日、購入者名と「出荷準備中」を登録
         */
        response()->json(Response::HTTP_CREATED);
    }

    use ImportCsv;

    /**
     * 入力した条件に該当する出荷情報を検索する。
     * @param ShiptLogRequest $request
     * @return Response
     */
    public function index(ShiptLogRequest $request) {
        $detail = $request->only([Header::BUYER, Header::SHIPPING_DATE]);
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

}
