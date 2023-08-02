<?php

namespace App\Http\Controllers;

use App\Exceptions\NotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\StockpileIndexRequest;
use App\Services\Constant\SearchConstant;
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
     * @param StockpileIndexRequest $request
     * @return json
     */
    public function index(StockpileIndexRequest $request) {
        logger()->info('Start Search Stockpile');
        $details = $request->only([SearchConstant::CARD_NAME, SearchConstant::SET_NAME]);
        $details[SearchConstant::LIMIT] = (int)$request->input(SearchConstant::LIMIT);
        logger()->debug('入力パラメータ', $details);
        $result = $this->service->fetch($details);
        if (count($result) == 0) {
            logger()->info('No Result Stockpile');
            throw new NotFoundException(Response::HTTP_NO_CONTENT, '在庫情報がありません');
        }
        
        logger()->info('End Search Stockpile');
        return response()->json($result, Response::HTTP_OK);
    }
}
