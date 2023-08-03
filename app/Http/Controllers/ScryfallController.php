<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ScryfallRequest;
use App\Services\ScryfallService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ScryfallController extends Controller
{
    public function __construct(ScryfallService $service)
    {
     $this->service = $service;   
    }
    public function index(ScryfallRequest $request) {
        $request->input(["setcode", "number","language"]);
        $card = $this->service->getCardInfoByNumber(
                $request->input(), $request->input("number"), $request->input("language"));
        return response()->json($card, Response::HTTP_OK);
    }
}
