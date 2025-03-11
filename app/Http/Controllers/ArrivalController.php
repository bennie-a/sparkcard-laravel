<?php

namespace App\Http\Controllers;

use App\Exceptions\api\NoContentException;
use App\Exceptions\NotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\ArrivalRequest;
use App\Http\Requests\ArrivalSearchRequest;
use App\Http\Resources\ArrivalLogResource;
use App\Http\Response\CustomResponse;
use App\Models\CardInfo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\Constant\StockpileHeader as Header;
use App\Services\Stock\ArrivalParams;
use App\Services\Stock\ArrivalLogService;
use App\Services\Constant\SearchConstant as Con;

use function PHPUnit\Framework\isEmpty;

/**
 * 入荷手続きAPI
 */
class ArrivalController extends Controller {

    private $service;
    public function __construct(ArrivalLogService $service) 
    {
        $this->service = $service;
    }

    /**
     * 入荷情報を検索する。
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ArrivalSearchRequest $request)
    {
        $details = $request->only([Con::CARD_NAME, Con::START_DATE, Con::END_DATE]);

        logger()->info('Start to search arrival log', $details);
        $results = $this->service->fetch($details);
        if ($results->isEmpty()) {
            logger()->info('No Result');
            throw new NoContentException();
        }
        $count = $results->count();
        logger()->info("End to search $count arrival log");
        $json = ArrivalLogResource::collection($results);
        return response($json, Response::HTTP_OK);
    }

    /**
     * 入荷情報と在庫情報を登録する。
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ArrivalRequest $request)
    {
        $details = $request->only([Header::CARD_ID, Header::LANGUAGE,  Header::QUANTITY, Header::COST,
        Header::MARKET_PRICE, Header::CONDITION, Header::VENDOR_TYPE_ID, Header::VENDOR]);
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
            \App\Facades\CardBoard::store($info, $details);
        }
        logger()->info('End Arrival log', [$params->cardId()]);
        return response()->json([Header::CARD_ID => $params->cardId(), 'arrival_id' => $arrivalLog->id], Response::HTTP_CREATED);
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
