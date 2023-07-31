<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\Stock\StockpileService;
use App\Traits\ImportCsv;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * 在庫管理機能API
 */
class StockpileController extends Controller
{
    public function __construct(StockpileService $service) {
        ini_set("max_execution_time",300); // タイムアウトを300秒にセット
        ini_set("max_input_time",300); // パース時間を300秒にセット
        $this->service = $service;
    }
    use ImportCsv;

    /**
     * 在庫情報を検索する。
     *
     * @param Request $request
     * @return json
     */
    public function index(Request $request) {
        $details = $request->only(['card_name', 'set_name']);
        $details['limit'] = (int)$request->input('limit');
        logger()->debug('入力パラメータ', $details);
        return response()->json(Response::HTTP_OK);
    }
}
