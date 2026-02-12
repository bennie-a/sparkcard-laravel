<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ScryfallRequest;
use App\Services\ScryfallService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\Constant\CardConstant as Con;
use App\Services\Constant\StockpileHeader as Header;

/**
 * Scryfall APIに接続するコントローラークラス
 */
class ScryfallController extends Controller
{
    private $service;
    public function __construct(ScryfallService $service)
    {
     $this->service = $service;   
    }
    public function index(ScryfallRequest $request) {
        $request->input([Header::SETCODE, Con::NUMBER, Header::LANGUAGE]);
        $card = $this->service->getCardInfoByNumber(
                $request->input(), $request->input(Con::NUMBER), Header::LANGUAGE);
        return response()->json($card, Response::HTTP_OK);
    }
}
