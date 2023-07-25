<?php

namespace App\Http\Controllers;

use App\Facades\CardBoard;
use App\Http\Controllers\Controller;
use App\Http\Requests\ArrivalRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\Constant\StockpileHeader as Header;
use App\Services\Stock\ArrivalParams;
use App\Services\Stock\ArrivalLogService;

/**
 * 入荷手続きAPI
 */
class ArrivalController extends Controller {

    public function __construct(ArrivalLogService $service) 
    {
        $this->service = $service;        
    }
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
        $details = $request->only(['card_id', 'language',  Header::QUANTITY, Header::COST,
                                                 Header::MARKET_PRICE, Header::CONDITION, Header::SUPPLIER]);
        $details[Header::IS_FOIL] = $request->boolean(Header::IS_FOIL);
        $details[Header::ARRIVAL_DATE] = $request->date(Header::ARRIVAL_DATE);
        $params = new ArrivalParams($details);
        $this->service->store($params);
        // \CardBoard::store($details);
        // logger()->info($details);
        return response()->json([], Response::HTTP_CREATED);
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
