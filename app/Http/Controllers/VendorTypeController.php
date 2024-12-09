<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\VendorType;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\Constant\CardConstant as Con;

/**
 * 仕入先タイプに関するControllerクラス
 */
class VendorTypeController extends Controller
{
    /**
     * 仕入先タイプを全て取得する。
     */
    public function index()
    {
        $all_type = VendorType::all(['id', Con::NAME])->sortBy(true);
        return response($all_type, Response::HTTP_OK);
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
