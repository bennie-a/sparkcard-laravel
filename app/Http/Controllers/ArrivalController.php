<?php

namespace App\Http\Controllers;

use App\Facades\CardBoard;
use App\Http\Controllers\Controller;
use App\Http\Requests\ArrivalRequest;
use App\Models\CardInfo;
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
        $details = $request->only([Header::CARD_ID, 'language',  Header::QUANTITY, Header::COST,
        Header::MARKET_PRICE, Header::CONDITION, Header::SUPPLIER]);
        $details[Header::IS_FOIL] = $request->boolean(Header::IS_FOIL);
        $details[Header::ARRIVAL_DATE] = $request->date(Header::ARRIVAL_DATE);
        $params = new ArrivalParams($details);
        logger()->info('Start Arrival log', [$params->cardId()]);
        $info = CardInfo::find($params->cardId());
        if (empty($info)) {
            throw new NotFoundException(CustomResponse::HTTP_NOT_FOUND_CARD, 'カード情報がありません');
        }

        $arrivalLog = $this->service->store($params);
        if (!empty($arrivalLog)) {
            \CardBoard::store($info, $details);
        }
        logger()->info('End Arrival log', [$params->cardId()]);
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
