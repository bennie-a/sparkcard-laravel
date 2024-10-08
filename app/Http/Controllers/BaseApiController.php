<?php

namespace App\Http\Controllers;

use App\Factory\GuzzleClientFactory;
use App\Http\Controllers\Controller;
use GuzzleHttp\TransferStats;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BaseApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $client_id = config('baseapi.client_id');
        $secret = config('baseapi.secret');
        $app_url = config('baseapi.app_url');
        $client = GuzzleClientFactory::create('base');

        $query = ["response_type" => "code",
                            "client_id" => $client_id,'redirect_uri' => $app_url, 'scope' => 'read_items'];

        $redirect_url = 'https://api.thebase.in/1/oauth/authorize?response_type=code&';
        $redirect_url .= "client_id=$client_id&redirect_uri=$app_url&'scope' => read_items";
        return redirect()->to($redirect_url);
        // $response = $client->get('1/oauth/authorize',  ['query' => $query, 'content-type' => 'application/json']);
        // $contents = $response->getBody()->getContents();
        
        // $code = explode('=', $contents)[1];
        // return response($contents, $response->getStatusCode());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
