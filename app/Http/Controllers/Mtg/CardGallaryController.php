<?php

namespace App\Http\Controllers\Mtg;

use App\Http\Controllers\Controller;
use App\Repositories\Api\Mtg\CardGallaryRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CardGallaryController extends Controller
{
    // public function __construct(CardGallaryRepository $repo) 
    // {
    //     $this->repo = $repo;
    // }
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $name
     * @return \Illuminate\Http\Response
     */
    public function show($name)
    {
        $repo = new CardGallaryRepository('dominaria-united');
        $code = Response::HTTP_OK;
        $color = $repo->getCardColor($name);
        $url = $repo->getImageUrl($name);
        $json = [$color, $url];
        return Response($json, $code);
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