<?php

namespace App\Http\Controllers\Notion;

use App\Http\Controllers\Controller;
use App\Http\Requests\Notion\GetCardboardRequest;
use App\Http\Requests\Notion\PostCardboardRequest;
use App\Http\Resources\Notion\NotionCardResource;
use App\Services\CardBoardService;
use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
/**
 * Notion操作に関するAPIクラス
 */
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
    public function index(GetCardboardRequest $request)
    {   
        logger()->info('get card by status...');
        $status = $request->input('status');
        $limitPrice = $request->input('limitPrice');
        $results = $this->service->findByStatus($status, $limitPrice);
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
    public function update(PostCardboardRequest $request, $id)
    {
        try {
            $details = $request->all();
            logger()->debug($details);

            $this->service->update($id, $details);
            return response("更新完了 ID:".$id, Response::HTTP_OK);
        } catch(Exception $e) {
            logger()->error($e);
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
