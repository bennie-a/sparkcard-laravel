<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CsvFileRequest;
use App\Services\CardInfoDBService;
use App\Services\Stock\StockpileService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TranslateController extends Controller
{

    public function __construct(StockpileService $service)
    {
        $this->service = $service;
    }
    public function index(CsvFileRequest $request) {
        $path = $request->input('path');
        $results = $this->service->getEnCard($path);
        return response()->json($results, Response::HTTP_OK);
    }
}
