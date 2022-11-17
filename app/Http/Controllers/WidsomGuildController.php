<?php

namespace App\Http\Controllers;

use App\Http\Resources\WisdomGuildResource;
use Illuminate\Http\Request;
use App\Services\WisdomGuildService;
use Illuminate\Http\Response;
class WidsomGuildController extends Controller
{
    private $service;
    public function __construct(WisdomGuildService $service)
    {
        ini_set("max_execution_time",180); // タイムアウトを180秒にセット
        ini_set("max_input_time",180); // パース時間を180秒にセット

        $this->service = $service;
    }
    /**
     * Display a listing of the resource.
     * @deprecated 2.0.0
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = $request->all();
        logger()->debug($query);
        $cardlist = $this->service->fetch($query);
        logger()->info("Get card info finished");
        $json = WisdomGuildResource::collection($cardlist);
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
        //
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
