<?php

namespace App\Http\Controllers;

use App\Facades\APIHand;
use App\Facades\Promo;
use App\Http\Controllers\Controller;
use App\Http\Requests\PromoSearchRequest;
use Illuminate\Http\Request;
use App\Services\Constant\StockpileHeader as Header;
use App\Services\PromoService;

/**
 * 特別版に関するControllerクラス
 */
class PromotypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(PromoSearchRequest $request)
    {
        $details = $request->only([Header::SETCODE]);
        $search = fn($details) => Promo::fetch($details);  // 検索処理
        $transformer = fn($results) => $results; // 変換処理
        return APIHand::handleSearch($details, $search, $transformer);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
