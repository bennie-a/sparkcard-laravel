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
        ini_set("max_execution_time",300); // タイムアウトを240秒にセット
        ini_set("max_input_time",300); // パース時間を240秒にセット
        $this->service = $service;
    }
    use ImportCsv;
}
