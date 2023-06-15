<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\stock\StockpileService;
use App\Traits\ImportCsv;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * 在庫管理機能API
 */
class StockpileController extends Controller
{
    public function __construct(StockpileService $service) {
        $this->service = $service;
    }
    use ImportCsv;
}
