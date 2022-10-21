<?php

namespace App\Http\Controllers\Notion;

use App\Http\Controllers\Controller;
use App\Http\Requests\Notion\NotionCardRequest;
use App\Http\Resources\Notion\NotionCardResource;
use App\Repositories\Api\Notion\ExpansionRepository;
use App\Services\CardBoardService;
use Exception;
use FiveamCode\LaravelNotionApi\Entities\Page;
use FiveamCode\LaravelNotionApi\Exceptions\NotionException;
use FiveamCode\LaravelNotionApi\Notion;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class CardController extends Controller
{
    private $service;

    public function __construct(CardBoardService $service)
    {
        $this->service = $service;
    }
    /**
     * 指定したステータスに一致するカードを取得する。
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        logger()->info('get card by status...');
        $status = $request->input('status');
        $details = $request->all();
        $results = $this->service->findByStatus($status, $details);
        if (array_key_exists('status', $results)) {
            logger()->info('Status:'.$results['status']);
            $res = response()->json($results, $results['status']);
            throw new HttpResponseException($res);
        }
        logger()->info(count($results).'件取得しました');
        $json = NotionCardResource::collection($results);
        return response()->json($json, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        $details = $request->all();
        logger()->debug("登録パラメータ", $details);
        $this->service->store($details);
        return Response::HTTP_OK;
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
    public function update(NotionCardRequest $request, $id)
    {
        try {
            $details = $request->all();
            $this->service->update($id, $details);
            return response("更新完了 ID:".$id, Response::HTTP_OK);
        } catch(Exception $e) {
            return response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
