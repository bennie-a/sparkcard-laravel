<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ScryfallRequest;
use App\Http\Resources\Api\ScryfallResource;
use App\Services\Api\ScryfallService;
use Illuminate\Http\Response;
use App\Services\Constant\GlobalConstant;

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
        $details = $request->only(GlobalConstant::DATA);
        $card = $this->service->getCardInfoByNumber($details[GlobalConstant::DATA]);
        return response()->json(new ScryfallResource($card), Response::HTTP_OK);
    }
}
