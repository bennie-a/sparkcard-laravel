<?php

namespace App\Http\Controllers;

use App\Exceptions\api\NoContentException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Arrival\ArrivalGroupRequest;
use App\Http\Requests\Arrival\ArrivalRequest;
use App\Http\Requests\Arrival\ArrivalSearchRequest;
use App\Http\Resources\ArrivalGroupingLogResource;
use App\Http\Resources\Arrival\ArrivalLogResource;
use App\Models\CardInfo;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\Constant\StockpileHeader as Header;
use App\Services\Stock\ArrivalParams;
use App\Services\Stock\ArrivalLogService;
use App\Services\Constant\SearchConstant as Con;
use App\Services\Constant\ArrivalConstant as ACon;
use App\Services\Constant\GlobalConstant as GCon;
use App\Facades\APIHand;
use App\Facades\CardBoard;
use App\Http\Requests\Arrival\ArrivalUpdateRequest;
use App\Http\Resources\Arrival\ArrivalLogFindResource;
use App\Http\Resources\Arrival\ArrivalLogUpdateResource;
use App\Models\ArrivalLog;
use App\Services\Constant\GlobalConstant;
use App\Services\Constant\SearchConstant;

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
        $details = $request->only([Con::CARD_NAME, ACon::ARRIVAL_DATE, Con::VENDOR_TYPE_ID]);
        $search = fn($details) => $this->service->filtering($details);  // 検索処理

        $transformer = fn($results) => [GCon::DATA => $results[GCon::DATA],
                                                                   GCon::LOGS => ArrivalLogResource::collection($results[GCon::LOGS])]; // 変換処理
        return APIHand::handleSearch($details, $search, $transformer);
    }

    /**
     * 設定した条件を元に入荷情報を取得する。
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function grouping(ArrivalGroupRequest $request) {
        $details = $request->only([Con::CARD_NAME, Con::START_DATE, Con::END_DATE]);
        $search = fn($details) => $this->service->fetch($details);  // 検索処理
        $transformer = fn($results) => ArrivalGroupingLogResource::collection($results); // 変換処理
        return APIHand::handleSearch($details, $search, $transformer);
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
        Header::MARKET_PRICE, Header::CONDITION, Con::VENDOR_TYPE_ID, ACon::VENDOR]);
        $details[Header::IS_FOIL] = $request->boolean(Header::IS_FOIL);
        $details[ACon::ARRIVAL_DATE] = $request->date(ACon::ARRIVAL_DATE);
        $params = new ArrivalParams($details);
        logger()->info('Start to Post Arrival log', [$params->cardId()]);
        $info = CardInfo::find($params->cardId());
        if (empty($info)) {
            throw new NoContentException();
        }

        $arrivalLog = $this->service->store($params);
        if (!empty($arrivalLog)) {
            \App\Facades\CardBoard::store($info, $details);
        }
        logger()->info('Start to Post Arrival log', [$params->cardId()]);
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
        $show = fn($id) => $this->service->findById($id);
        $transformer = fn($model) => new ArrivalLogFindResource($model);
        return APIHand::handleShow($id, $show, $transformer);
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
        logger()->info("Start to Update id:{$id}");
        // $details = $request->only([Header::QUANTITY, ACon::ARRIVAL_DATE, 
        //                                     SearchConstant::VENDOR_TYPE_ID, ACon::VENDOR, Header::COST]);
        $details = $request->all();
        $isExists = ArrivalLog::where(GlobalConstant::ID, $id)->exists();
        if (!$isExists) {
            logger()->info("No Result  id:{$id}");
            throw new NoContentException();
        }
        $result = $this->service->update($id, $details);
        logger()->info("End to update id:{$id}");
        return response(new ArrivalLogFindResource($result), Response::HTTP_OK);
    }

    /**
     * 入荷情報を削除する。
     *
     * @param  int  $id 入荷ID
     * @return \Illuminate\Http\Response 
     */
    public function destroy($id)
    {
        logger()->info("Start to Delete Arrival log. id:{$id}");
        $log = $this->service->findByStockInfo($id);
        if (empty($log)) {
            throw new NoContentException();
        }
        CardBoard::decreaseQuantity($log->card_id, $log->quantity);
        $this->service->destroy($log);
        logger()->info("End to Delete Arrival log. id:{$id}");
        return response()->noContent();
    }
}
