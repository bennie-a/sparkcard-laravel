<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShiptLogRequest;
use App\Traits\ImportCsv;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\Constant\StockpileHeader as Header;
use App\Services\Shipt\ShiptLogService;
use App\Services\Constant\ShiptConstant as ShiptCon;

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
     * @param Request $request
     * @return void
     */
    public function parse(Request $request) {
        $file = $request->file('file');
        logger()->info("ファイル読み込み開始：{$file->getClientOriginalName()}");

        $records = $this->service->parse($file->getRealPath());
        $data = [
            "order_id" => "order_Nt7GwWt9TQj3zb5AGhrNTJ",
            "buyer_name" => "金井 三郎",
            "shipping_date" => "2025/10/10",
            "zipcode" => "899-4754",
            "address" => "福井県吉田郡永平寺町諏訪間502-6",
            "items" => [
                [
                    "id" => 2473,
                    "card" => [
                        "name" => "次元の創世",
                        "exp" => [
                            "name" => "モダンホライゾン3",
                            "attr" => "MH3"
                        ],
                        "color" => "多色",
                        "foil" => [
                            "is_foil" => false,
                            "name" => "通常版"
                        ],
                        "promotype" => [
                            "id" => 1,
                            "name" => ""
                        ]
                    ],
                    "condition" => "NM",
                    "quantity" => 2,
                    "lang" => "JP",
                    "single_price" => 6000,
                    "subtotal_price" => 12000,
                ],
                [
                    "id" => 2957,
                    "card" => [
                        "name" => "次元の創世",
                        "exp" => [
                            "name" => "モダンホライゾン3",
                            "attr" => "MH3"
                        ],
                        "color" => "多色",
                        "foil" => [
                            "is_foil" => false,
                            "name" => "通常版"
                        ],
                        "promotype" => [
                            "id" => 1,
                            "name" => ""
                        ]
                    ],
                    "lang" => "JP",
                    "condition" => "NM-",
                    "quantity" => 1,
                    "single_price" => 120,
                    "subtotal_price" => 120,
                ],
            ]
        ];

        return response($records, Response::HTTP_CREATED);
    }

}
