<?php

namespace App\Http\Controllers;

use App\Facades\CardBoard;
use App\Http\Controllers\Controller;
use App\Http\Requests\ArrivalRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\Constant\StockpileHeader as Header;

/**
 * 入荷手続きAPI
 */
class ArrivalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ArrivalRequest $request)
    {
        // $details = $request->only(['card_id', 'language',  Header::QUANTITY, 'cost', 'market_price', 'condition']);
        // $details[Header::IS_FOIL] = $request->boolean(Header::IS_FOIL);
        // \CardBoard::store($details);
        // logger()->info($details);
        return response(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
