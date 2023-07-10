<?php

namespace App\Http\Controllers;

use App\Facades\CardBoard;
use App\Http\Controllers\Controller;
use App\Http\Requests\ArrivalRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
        $details = $request->only(['id', 'name','enname','index','price','attr','color','imageUrl','quantity','isFoil','language','condition']);
        \CardBoard::store($details);
        logger()->info($details);
        return response(Response::HTTP_OK);
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
